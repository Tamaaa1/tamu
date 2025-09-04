<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\AgendaDetail;
use App\Models\MasterDinas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AgendaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['showPublic', 'showPublicAgenda', 'registerParticipant']);
    }

    // Menampilkan Daftar dari Resource
    public function index(Request $request)
    {
        $query = Agenda::with(['masterDinas', 'koordinator'])->latest();

        // Filter berdasarkan tanggal, bulan, dan tahun
        if ($request->filled('tanggal')) {
            $query->whereDay('tanggal_agenda', $request->tanggal);
        }
        
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal_agenda', $request->bulan);
        }
        
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_agenda', $request->tahun);
        }

        $agendas = $query->get();
        return view('admin.agenda.index', compact('agendas'));
    }

    // Menampilkan Form untuk membuat resource baru
    public function create()
    {
        $dinas = MasterDinas::all();
        return view('admin.agenda.create', compact('dinas'));
    }

    // Menampilkan Form untuk menyimpan resource baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'dinas_id' => 'required|exists:master_dinas,dinas_id',
            'nama_agenda' => 'required|string|max:255',
            'tanggal_agenda' => 'required|date',
            'link_acara' => 'nullable|string'
        ]);

        $validated['nama_koordinator'] = Auth::user()->name ?? Auth::user()->username;
        $validated['link_active'] = $request->has('link_active') ? true : false;

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
        $agenda->load(['masterDinas', 'koordinator']);
        
        // Generate QR code for the public registration URL
        $publicUrl = route('agenda.public.register', $agenda);
        $qrCode = QrCode::size(200)->generate($publicUrl);
        
        return view('admin.agenda.qrcode', compact('agenda', 'qrCode', 'publicUrl'));
    }

    // Export QR Code ke PDF dan simpan link registrasi ke database
    public function exportQrCodePdf(Agenda $agenda)
    {
        $agenda->load(['masterDinas', 'koordinator']);
        
        // QR Code untuk URL pendaftaran publik
        $publicUrl = route('agenda.public.register', $agenda);
        $qrCodeSvg = QrCode::size(200)->generate($publicUrl);
        
        // Convert SVG to base64
        $qrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg);
        
        // Simpan link registrasi ke database
        $agenda->update(['link_acara' => $publicUrl]);
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.agenda.export-pdf', [
            'agenda' => $agenda,
            'qrCodeBase64' => $qrCodeBase64,
            'publicUrl' => $publicUrl
        ]);
        
        return $pdf->download('qrcode-agenda-' . $agenda->id . '.pdf');
    }

    public function showPublic()
    {
        // Ambil agenda untuk hari ini yang aktif
        $today = now()->format('Y-m-d');
        $agenda = Agenda::whereDate('tanggal_agenda', $today)
            ->where('link_active', true)
            ->orderBy('nama_agenda')
            ->first();

        // Jika tidak ada agenda hari ini yang aktif, tampilkan halaman no-agenda
        if (!$agenda) {
            return view('participant.no-agenda');
        }
        
        $dinas = MasterDinas::all();
        
        // Ambil semua agenda pada tanggal yang sama (hari ini) yang aktif untuk dropdown pilihan
        $agendasOnSameDate = Agenda::whereDate('tanggal_agenda', $today)
            ->where('link_active', true)
            ->orderBy('nama_agenda')
            ->get();
            
        return view('participant.public-register', compact('agenda', 'dinas', 'agendasOnSameDate'));
    }

    // Menampilkan agenda publik untuk pendaftaran
    public function showPublicAgenda($agendaId)
    {
        // Cari agenda berdasarkan ID yang aktif, jika tidak ditemukan atau tidak aktif tampilkan halaman no-agenda
        $agenda = Agenda::where('id', $agendaId)
            ->where('link_active', true)
            ->first();

        if (!$agenda) {
            return view('participant.no-agenda');
        }
        
        $dinas = MasterDinas::all();
        
        // Ambil semua agenda pada tanggal yang sama yang aktif untuk dropdown pilihan
        $agendasOnSameDate = Agenda::whereDate('tanggal_agenda', $agenda->tanggal_agenda)
            ->where('link_active', true)
            ->orderBy('nama_agenda')
            ->get();
            
        return view('participant.public-register', compact('agenda', 'dinas', 'agendasOnSameDate'));
    }

    // Menampilkan Form untuk mendaftar peserta (akses publik)
    public function registerParticipant(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|min:2|max:100',
            'jabatan' => 'required|string',
            'no_hp' => 'required|string|regex:/^[0-9]+$/|min:10|max:13',
            'dinas_id' => 'required|exists:master_dinas,dinas_id',
            'signature' => 'required|string',
            'agenda_id' => 'required|exists:agendas,id'
        ], [
            'nama.required' => 'Nama harus diisi',
            'nama.min' => 'Nama minimal 2 karakter',
            'nama.max' => 'Nama maksimal 100 karakter',
            'jabatan.required' => 'Jabatan harus diisi',
            'no_hp.required' => 'Nomor HP harus diisi',
            'no_hp.regex' => 'Nomor HP hanya boleh berisi angka',
            'no_hp.min' => 'Nomor HP minimal 10 digit',
            'no_hp.max' => 'Nomor HP maksimal 13 digit',
            'dinas_id.required' => 'Dinas harus dipilih',
            'dinas_id.exists' => 'Dinas yang dipilih tidak valid',
            'signature.required' => 'Tanda tangan harus diisi'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Convert tanda tangan Base64 menjadi file
        $signaturePath = null;
        if ($request->signature) {
            $signatureData = $request->signature;

            // Ekstrak data base64
            if (preg_match('/^data:image\/(png|jpg|jpeg);base64,/', $signatureData, $matches)) {
                $base64Data = substr($signatureData, strpos($signatureData, ',') + 1);
                $imageData = base64_decode($base64Data);

                // Buat nama file yang unik - selalu gunakan format PNG
                $filename = 'signature_' . time() . '_' . uniqid() . '.png';
                $path = 'tandatangan/' . $filename;

                // Simpan file
                Storage::disk('public')->put($path, $imageData);
                $signaturePath = $path;
            }
        }

        // Generate QR Code data (simpan data ini saja)
        $qrData = json_encode([
            'nama' => $request->nama,
            'jabatan' => $request->jabatan,
            'no_hp' => $request->no_hp,
            'dinas_id' => $request->dinas_id,
            'agenda_id' => $request->agenda_id,
            'timestamp' => now()->toISOString()
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        // Generate QR Code sebagai SVG untuk response
        $qrCodeSvg = QrCode::size(200)->generate($qrData);
        
        // Konversi SVG ke base64 untuk display di HTML
        $qrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg);

        // Save participant data - simpan hanya data JSON untuk QR code
        $participant = AgendaDetail::create([
            'agenda_id' => $request->agenda_id,
            'nama' => $request->nama,
            'dinas_id' => $request->dinas_id,
            'jabatan' => $request->jabatan,
            'no_hp' => $request->no_hp,
            'gambar_ttd' => $signaturePath, 
            'qr_code' => $qrData
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disimpan!',
            'participant' => $participant
        ]);
    }
}
