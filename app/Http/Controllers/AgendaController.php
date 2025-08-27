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
        $this->middleware('auth')->except(['showPublic', 'registerParticipant']);
    }

    /**
     * Display a listing of the resource.
     */
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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $dinas = MasterDinas::all();
        return view('admin.agenda.create', compact('dinas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'dinas_id' => 'required|exists:master_dinas,dinas_id',
            'nama_agenda' => 'required|string|max:255',
            'tanggal_agenda' => 'required|date',
            'link_acara' => 'string|unique:agendas,link_acara'
        ]);

        $validated['nama_koordinator'] = Auth::user()->name ?? Auth::user()->username;

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
            'link_acara' => 'string|unique:agendas,link_acara,' . $agenda->id
        ]);

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

    public function showPublic()
    {
        // Ambil agenda terbaru berdasarkan tanggal
        $agenda = Agenda::where('tanggal_agenda', '>=', now()->format('Y-m-d'))
            ->orderBy('tanggal_agenda', 'asc')
            ->first();
        
        // Jika tidak ada agenda yang akan datang, ambil agenda terbaru yang sudah lewat
        if (!$agenda) {
            $agenda = Agenda::orderBy('tanggal_agenda', 'desc')->first();
        }
        
        $dinas = MasterDinas::all();
        
        // Jika tidak ada agenda sama sekali, redirect ke halaman dengan pesan
        if (!$agenda) {
            return view('agenda.no-agenda');
        }
        
        return view('agenda.public-register', compact('agenda', 'dinas'));
    }

    /**
     * Register participant (public access)
     */
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

        // Convert Base64 signature to file
        $signaturePath = null;
        if ($request->signature) {
            $signatureData = $request->signature;
            
            // Extract the base64 data from the data URL
            if (preg_match('/^data:image\/(png|jpg|jpeg);base64,/', $signatureData, $matches)) {
                $imageType = $matches[1];
                $base64Data = substr($signatureData, strpos($signatureData, ',') + 1);
                $imageData = base64_decode($base64Data);
                
                // Generate unique filename
                $filename = 'signature_' . time() . '_' . uniqid() . '.' . $imageType;
                $path = 'tandatangan/' . $filename;
                
                // Store the file
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
            'qr_code' => $qrCodeBase64, 
            'participant' => $participant
        ]);
    }

    /**
     * Export participants to Excel
     */
    public function exportExcel(Agenda $agenda)
    {
        // Implementation for Excel export
        return response()->json(['message' => 'Excel export feature akan diimplementasikan']);
    }

    /**
     * Export participants to PDF
     */
    public function exportPdf(Agenda $agenda)
    {
        // Implementation for PDF export
        return response()->json(['message' => 'PDF export feature akan diimplementasikan']);
    }
}
