<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\AgendaDetail;
use App\Models\MasterDinas;
use App\Traits\Filterable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Helpers\SignatureHelper;

class AgendaController extends Controller
{
    use Filterable;

    public function __construct()
    {
        $this->middleware('auth');
    }

    // Menampilkan daftar agenda dengan filter
    public function index(Request $request)
    {
        // Membuat query untuk mengambil data agenda beserta relasi master dinas dan koordinator
        $query = Agenda::with(['masterDinas', 'koordinator'])->latest();

        // Menerapkan filter tanggal menggunakan trait Filterable
        $query = $this->applyDateFilters($query, $request, 'tanggal_agenda');

        // Mengambil data agenda dengan pagination 15 data per halaman
        $agendas = $query->paginate(15);

        // Mengembalikan view dengan data agenda
        return view('admin.agenda.index', compact('agendas'));
    }

    // Form tambah agenda baru
    public function create()
    {
        // Mengambil semua data master dinas untuk dropdown pada form
        $dinas = MasterDinas::all();

        // Mengembalikan view form tambah agenda dengan data master dinas
        return view('admin.agenda.create', compact('dinas'));
    }

    // Simpan agenda baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'dinas_id' => 'required|exists:master_dinas,dinas_id',
            'nama_agenda' => 'required|string|max:255',
            'tanggal_agenda' => 'required|date',
            'link_acara' => 'nullable|string'
        ]);

        $validated['nama_koordinator'] = Auth::user()->name ?? Auth::user()->username; // Set koordinator otomatis
        $validated['link_active'] = $request->has('link_active') ? true : false; // Status aktif/nonaktif

        Agenda::create($validated);

        return redirect()->route('admin.agenda.index')
            ->with('success', 'Agenda berhasil dibuat!');
    }

    public function show(Agenda $agenda)
    {
        $agenda->load(['masterDinas', 'koordinator', 'agendaDetail.masterDinas']);
        return view('admin.agenda.show', compact('agenda'));
    }

    public function edit(Agenda $agenda)
    {
        $dinas = MasterDinas::all();
        return view('admin.agenda.edit', compact('agenda', 'dinas'));
    }

    public function update(Request $request, Agenda $agenda)
    {
        $validated = $request->validate([
            'dinas_id' => 'required|exists:master_dinas,dinas_id',
            'nama_agenda' => 'required|string|max:255',
            'tanggal_agenda' => 'required|date',
            'link_acara' => 'nullable|string'
        ]);

        $validated['link_active'] = $request->has('link_active') ? true : false;

        $agenda->update($validated);

        return redirect()->route('admin.agenda.index')
            ->with('success', 'Agenda berhasil diupdate!');
    }

    public function destroy(Agenda $agenda)
    {
        $agenda->delete();

        return redirect()->route('admin.agenda.index')
            ->with('success', 'Agenda berhasil dihapus!');
    }

    // Toggle link active status
    public function toggleLinkActive(Agenda $agenda)
    {
        $agenda->update(['link_active' => !$agenda->link_active]);

        return redirect()->route('admin.agenda.index')
            ->with('success', 'Status link agenda berhasil diubah!');
    }

    // Tampilkan QR code untuk agenda
    public function showQrCode(Agenda $agenda)
    {
        // Memuat relasi master dinas dan koordinator untuk agenda
        $agenda->load(['masterDinas', 'koordinator']);

        // Pastikan agenda memiliki token unik, jika belum buat token baru
        if (empty($agenda->unique_token)) {
            $agenda->unique_token = Agenda::generateUniqueToken();
            $agenda->save();
        }

        // Generate QR code untuk URL pendaftaran publik dengan token unik
        $publicUrl = route('agenda.public.register.token', ['token' => $agenda->unique_token]);
        $qrCode = QrCode::size(200)->generate($publicUrl);

        // Kembalikan view dengan data agenda, QR code, dan URL publik
        return view('admin.agenda.qrcode', compact('agenda', 'qrCode', 'publicUrl'));
    }

    // Export QR Code ke PDF dan simpan link registrasi ke database
    public function exportQrCodePdf(Agenda $agenda)
    {
        // Memuat relasi master dinas dan koordinator untuk agenda
        $agenda->load(['masterDinas', 'koordinator']);

        // Pastikan agenda memiliki token unik, jika belum buat token baru
        if (empty($agenda->unique_token)) {
            $agenda->unique_token = Agenda::generateUniqueToken();
            $agenda->save();
        }

        // Generate QR Code untuk URL pendaftaran publik dengan token unik
        $publicUrl = route('agenda.public.register.token', ['token' => $agenda->unique_token]);
        $qrCodeSvg = QrCode::size(200)->generate($publicUrl);

        // Konversi SVG QR Code ke base64 untuk embed di PDF
        $qrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg);

        // Simpan link registrasi ke database hanya jika link_acara kosong
        if (empty($agenda->link_acara)) {
            $agenda->update(['link_acara' => $publicUrl]);
        }

        // Generate PDF dengan view khusus yang menampilkan QR Code
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.agenda.export-pdf', [
            'agenda' => $agenda,
            'qrCodeBase64' => $qrCodeBase64,
            'publicUrl' => $publicUrl
        ]);

        // Download file PDF dengan nama khusus
        return $pdf->download('qrcode-agenda-' . $agenda->id . '.pdf');
    }


}
