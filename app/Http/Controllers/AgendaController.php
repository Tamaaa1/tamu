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

/**
 * Controller untuk mengelola agenda rapat/acara
 *
 * Menangani operasi CRUD agenda, QR code generation,
 * dan manajemen status aktif/nonaktif agenda.
 *
 * @package App\Http\Controllers
 */
class AgendaController extends Controller
{
    use Filterable;

    /**
     * Membuat instance controller baru
     *
     * Menerapkan middleware autentikasi untuk semua method
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Menampilkan daftar agenda dengan filter tanggal
     *
     * Mengambil data agenda beserta relasi master dinas dan koordinator,
     * menerapkan filter tanggal, dan menampilkan dengan pagination.
     *
     * @param Request $request Request object yang berisi parameter filter
     * @return \Illuminate\View\View View daftar agenda
     */
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

    /**
     * Menampilkan form untuk membuat agenda baru
     *
     * Mengambil data master dinas dari cache dan menampilkan form create agenda.
     *
     * @return \Illuminate\View\View View form create agenda
     */
    public function create()
    {
        // Mengambil semua data master dinas untuk dropdown pada form dengan caching
        $dinas = Cache::remember('master_dinas', 3600, function () { // Cache 1 jam
            return MasterDinas::all();
        });

        // Mengembalikan view form tambah agenda dengan data master dinas
        return view('admin.agenda.create', compact('dinas'));
    }

    /**
     * Menyimpan agenda baru ke database
     *
     * Melakukan validasi input, mengatur koordinator otomatis,
     * dan membuat agenda baru dengan cache invalidation.
     *
     * @param Request $request Data request dari form
     * @return \Illuminate\Http\RedirectResponse Redirect ke index dengan pesan sukses
     */
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

        // Clear cache agendas setelah create
        Cache::forget('agendas_all');

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
        $dinas = Cache::remember('master_dinas', 3600, function () { // Cache 1 jam
            return MasterDinas::all();
        });
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

        // Clear cache agendas setelah update
        Cache::forget('agendas_all');

        return redirect()->route('admin.agenda.index')
            ->with('success', 'Agenda berhasil diupdate!');
    }

    public function destroy(Agenda $agenda)
    {
        $agenda->delete();

        // Clear cache agendas setelah delete
        Cache::forget('agendas_all');

        return redirect()->route('admin.agenda.index')
            ->with('success', 'Agenda berhasil dihapus!');
    }

    // Toggle link active status
    public function toggleLinkActive(Agenda $agenda)
{
    try {
        // Toggle status link_active
        $newStatus = !$agenda->link_active;
        $agenda->update(['link_active' => $newStatus]);

        // Clear cache agendas setelah toggle status
        Cache::forget('agendas_all');

        // Prepare response data
        $responseData = [
            'success' => true,
            'link_active' => $newStatus,
            'message' => $newStatus 
                ? 'Link agenda berhasil diaktifkan!' 
                : 'Link agenda berhasil dinonaktifkan!'
        ];

        // Jika link diaktifkan, sertakan URL QR Code
        if ($newStatus) {
            $responseData['qrcode_url'] = route('admin.agenda.qrcode', $agenda);
        }

        // Check if request is AJAX
        if (request()->ajax()) {
            return response()->json($responseData);
        }

        // Fallback untuk non-AJAX request
        return redirect()->route('admin.agenda.index')
            ->with('success', $responseData['message']);

    } catch (\Exception $e) {
        // Log error untuk debugging
        Log::error('Error toggling agenda link status: ' . $e->getMessage(), [
            'agenda_id' => $agenda->id,
            'user_id' => Auth::id(),
            'error' => $e->getTraceAsString()
        ]);

        $errorMessage = 'Terjadi kesalahan saat mengubah status link agenda.';

        // Check if request is AJAX
        if (request()->ajax()) {
            return response()->json([
                'success' => false,
                'message' => $errorMessage
            ], 500);
        }

        // Fallback untuk non-AJAX request
        return redirect()->route('admin.agenda.index')
            ->with('error', $errorMessage);
    }
}

    /**
     * Menampilkan halaman QR code untuk agenda
     *
     * Menggenerate QR code untuk URL pendaftaran publik agenda
     * dan menampilkan halaman dengan QR code tersebut.
     *
     * @param Agenda $agenda Instance agenda yang akan ditampilkan QR codenya
     * @return \Illuminate\View\View View halaman QR code
     */
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

    /**
     * Mengekspor QR code agenda ke file PDF
     *
     * Menggenerate QR code, menyimpan link registrasi ke database,
     * dan mengekspor ke file PDF untuk didownload.
     *
     * @param Agenda $agenda Instance agenda yang akan diekspor QR codenya
     * @return \Illuminate\Http\Response File PDF download response
     */
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
    