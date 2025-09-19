<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\AgendaDetail;
use App\Models\MasterDinas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use App\Helpers\SignatureHelper;

class ParticipantController extends Controller
{
    public function index(Request $request)
    {
        $query = AgendaDetail::with(['agenda', 'masterDinas'])->orderBy('created_at', 'asc');

        // Apply filters
        $this->applyFilters($query, $request);

        $participants = $query->paginate(10);

        // Cache daftar agenda untuk performa
        $agendas = Cache::remember('agendas_all', 1800, function () { // Cache 30 menit
            return Agenda::select('id', 'nama_agenda', 'tanggal_agenda')->orderBy('tanggal_agenda', 'asc')->get();
        });

        // Check if this is an AJAX request for dynamic loading
        if ($request->ajax() || $request->has('ajax')) {
            return $this->getParticipantsData($request);
        }

        return view('admin.participants.index', compact('participants', 'agendas'));
    }

    private function applyFilters($query, $request)
    {
        // Filter berdasarkan date range yang lebih efisien
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        } elseif ($request->filled('start_date')) {
            $query->where('created_at', '>=', $request->start_date . ' 00:00:00');
        } elseif ($request->filled('end_date')) {
            $query->where('created_at', '<=', $request->end_date . ' 23:59:59');
        }

        // Backward compatibility untuk filter lama (tetap support tapi kurang optimal)
        if ($request->filled('tanggal') && $request->filled('bulan') && $request->filled('tahun')) {
            $date = sprintf('%04d-%02d-%02d', $request->tahun, $request->bulan, $request->tanggal);
            $query->whereDate('created_at', $date);
        }

        if ($request->filled('agenda_id')) {
            $query->where('agenda_id', $request->agenda_id);
        }
    }

    public function create()
    {
        // Cache daftar agenda dan dinas untuk performa
        $agendas = Cache::remember('agendas_all', 1800, function () { // Cache 30 menit
            return Agenda::select('id', 'nama_agenda', 'tanggal_agenda')->orderBy('tanggal_agenda', 'asc')->get();
        });
        $dinas = Cache::remember('master_dinas', 3600, function () { // Cache 1 jam
            return MasterDinas::all();
        });
        return view('admin.participants.create', compact('agendas', 'dinas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'agenda_id' => 'required|exists:agendas,id',
            'nama' => 'required|string|max:255',
            'dinas_id' => 'required|exists:master_dinas,dinas_id',
            'jabatan' => 'required|string|max:255',
            'gender' => 'required|in:Laki-laki,Perempuan',
            'no_hp' => 'required|string|regex:/^[0-9]+$/|min:10|max:13',
            'gambar_ttd' => 'nullable',
        ]);

        // Handle signature pad data (base64 image)
        if ($request->filled('gambar_ttd') && strpos($request->gambar_ttd, 'data:image/') === 0) {
            // Validasi tipe konten dan ukuran file
            $signatureData = $request->gambar_ttd;

            // Cek tipe file (hanya PNG/JPG)
            if (!preg_match('/^data:image\/(png|jpg|jpeg);base64,/', $signatureData)) {
                return back()->withErrors(['gambar_ttd' => 'Tipe file tanda tangan harus PNG atau JPG.'])->withInput();
            }

            // Hitung ukuran file dari base64
            $base64Data = substr($signatureData, strpos($signatureData, ',') + 1);
            $fileSize = (strlen($base64Data) * 3 / 4); // Approximate decoded size

            if ($fileSize > 2097152) { // 2MB in bytes
                return back()->withErrors(['gambar_ttd' => 'Ukuran file tanda tangan maksimal 2MB.'])->withInput();
            }

            // Simpan sebagai file menggunakan SignatureHelper (seperti halaman pendaftaran tamu)
            try {
                $signaturePath = SignatureHelper::processSignature($signatureData);
                $validated['gambar_ttd'] = $signaturePath;
            } catch (\Exception $e) {
                return back()->withErrors(['gambar_ttd' => 'Gagal menyimpan tanda tangan: ' . $e->getMessage()])->withInput();
            }
        } else {
            $validated['gambar_ttd'] = null;
        }

        AgendaDetail::create($validated);

        return redirect()->route('admin.participants.index')
            ->with('success', 'Peserta berhasil ditambahkan!');
    }

    public function show(AgendaDetail $participant)
    {
        $participant->load(['agenda', 'masterDinas']);
        return view('admin.participants.show', compact('participant'));
    }

    public function edit(AgendaDetail $participant)
    {
        // Cache daftar agenda dan dinas untuk performa
        $agendas = Cache::remember('agendas_all', 1800, function () { // Cache 30 menit
            return Agenda::select('id', 'nama_agenda', 'tanggal_agenda')->orderBy('tanggal_agenda', 'asc')->get();
        });
        $dinas = Cache::remember('master_dinas', 3600, function () { // Cache 1 jam
            return MasterDinas::all();
        });
        return view('admin.participants.edit', compact('participant', 'agendas', 'dinas'));
    }

    public function update(Request $request, AgendaDetail $participant)
    {
        $validated = $request->validate([
            'agenda_id' => 'required|exists:agendas,id',
            'nama' => 'required|string|max:255',
            'dinas_id' => 'required|exists:master_dinas,dinas_id',
            'jabatan' => 'required|string|max:255',
            'gender' => 'required|in:Laki-laki,Perempuan',
            'no_hp' => 'required|string|regex:/^[0-9]+$/|min:10|max:13',
            'gambar_ttd' => 'nullable',
        ]);

        // Handle signature pad data (base64 image)
        if ($request->filled('gambar_ttd') && strpos($request->gambar_ttd, 'data:image/') === 0) {
            // Validasi tipe konten dan ukuran file
            $signatureData = $request->gambar_ttd;

            // Cek tipe file (hanya PNG/JPG)
            if (!preg_match('/^data:image\/(png|jpg|jpeg);base64,/', $signatureData)) {
                return back()->withErrors(['gambar_ttd' => 'Tipe file tanda tangan harus PNG atau JPG.'])->withInput();
            }

            // Hitung ukuran file dari base64
            $base64Data = substr($signatureData, strpos($signatureData, ',') + 1);
            $fileSize = (strlen($base64Data) * 3 / 4); // Approximate decoded size

            if ($fileSize > 2097152) { // 2MB in bytes
                return back()->withErrors(['gambar_ttd' => 'Ukuran file tanda tangan maksimal 2MB.'])->withInput();
            }

            // Hapus tanda tangan lama jika ada dan bukan base64
            if ($participant->gambar_ttd && !str_contains($participant->gambar_ttd, 'data:image/')) {
                SignatureHelper::deleteSignature($participant->gambar_ttd);
            }

            // Simpan sebagai file menggunakan SignatureHelper (seperti halaman pendaftaran tamu)
            try {
                $signaturePath = SignatureHelper::processSignature($signatureData);
                $validated['gambar_ttd'] = $signaturePath;
            } catch (\Exception $e) {
                return back()->withErrors(['gambar_ttd' => 'Gagal menyimpan tanda tangan: ' . $e->getMessage()])->withInput();
            }
        } else {
            // Jika tidak ada tanda tangan baru, pertahankan yang lama
            $validated['gambar_ttd'] = $participant->gambar_ttd;
        }

        $participant->update($validated);

        return redirect()->route('admin.participants.index')
            ->with('success', 'Peserta berhasil diupdate!');
    }

    public function destroy(AgendaDetail $participant)
    {
        // Hapus file tanda tangan jika ada dan bukan base64
        if ($participant->gambar_ttd && !str_contains($participant->gambar_ttd, 'data:image/')) {
            SignatureHelper::deleteSignature($participant->gambar_ttd);
        }

        $participant->delete();

        return redirect()->route('admin.participants.index')
            ->with('success', 'Peserta berhasil dihapus!');
    }

    /**
     * Get participants data for AJAX requests (dynamic loading)
     */
    public function getParticipantsData(Request $request)
    {
        $query = AgendaDetail::with(['agenda', 'masterDinas'])->orderBy('created_at', 'asc');

        // Apply filters using the same method
        $this->applyFilters($query, $request);

        $participants = $query->paginate(10);
        $participants->withPath(route('admin.participants.index'))->appends($request->all());

        // Format data untuk response JSON
        $data = $participants->map(function ($participant) {
            return [
                'id' => $participant->id,
                'nama' => $participant->nama,
                'jabatan' => $participant->jabatan,
                'gender' => $participant->gender,
                'no_hp' => $participant->no_hp ?? '-',
                'dinas' => $participant->masterDinas->nama_dinas ?? '-',
                'agenda' => $participant->agenda->nama_agenda ?? '-',
                'tanggal_agenda' => $participant->agenda->tanggal_agenda_formatted ?? '-',
                'status' => $participant->agenda->status ?? '-',
                'gambar_ttd' => $participant->gambar_ttd ? asset('storage/' . $participant->gambar_ttd) : null,
                'created_at' => $participant->created_at->format('d/m/Y H:i'),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'pagination' => [
                'current_page' => $participants->currentPage(),
                'last_page' => $participants->lastPage(),
                'per_page' => $participants->perPage(),
                'total' => $participants->total(),
                'from' => $participants->firstItem(),
                'to' => $participants->lastItem(),
                'has_pages' => $participants->hasPages(),
                'links' => (string) $participants->links(),
            ],
            'filters' => [
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'tanggal' => $request->tanggal,
                'bulan' => $request->bulan,
                'tahun' => $request->tahun,
                'agenda_id' => $request->agenda_id,
            ]
        ]);
    }

    /**
     * Search agenda for AJAX dropdown
     */
    public function searchAgenda(Request $request)
    {
        $search = $request->get('q', '');
        
        $agendas = Agenda::select('id', 'nama_agenda')
            ->when($search, function ($query) use ($search) {
                return $query->where('nama_agenda', 'like', '%' . $search . '%');
            })
            ->orderBy('nama_agenda')
            ->limit(10)
            ->get();

        return response()->json($agendas);
    }
}
