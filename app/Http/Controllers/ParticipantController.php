<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\AgendaDetail;
use App\Models\MasterDinas;
use App\Services\SignatureService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
<<<<<<< HEAD

=======
use App\Helpers\SignatureHelper;
>>>>>>> 284e251ce60564e812888c40ae43c01b7d4a7614

class ParticipantController extends Controller
{
    public function index(Request $request)
    {
        $query = AgendaDetail::with(['agenda', 'masterDinas'])->orderBy('created_at', 'asc');

<<<<<<< HEAD
        $this->applyParticipantFilters($query, $request);

        $participants = $query->paginate(10);
        $agendas = Agenda::orderBy('tanggal_agenda', 'asc')->get();

        return view('admin.participants.index', compact('participants', 'agendas'));
=======
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
>>>>>>> 284e251ce60564e812888c40ae43c01b7d4a7614
    }

    public function create()
    {
<<<<<<< HEAD
        // Cache agendas for 30 minutes (form data doesn't need real-time updates)
        $agendas = Cache::remember('agendas_for_participants_form', now()->addMinutes(30), function () {
            return Agenda::orderBy('tanggal_agenda', 'asc')->get();
        });

        // Cache master dinas for 1 hour (master data changes infrequently)
        $dinas = Cache::remember('master_dinas_all', now()->addHour(), function () {
            return MasterDinas::all();
        });

=======
        // Cache daftar agenda dan dinas untuk performa
        $agendas = Cache::remember('agendas_all', 1800, function () { // Cache 30 menit
            return Agenda::select('id', 'nama_agenda', 'tanggal_agenda')->orderBy('tanggal_agenda', 'asc')->get();
        });
        $dinas = Cache::remember('master_dinas', 3600, function () { // Cache 1 jam
            return MasterDinas::all();
        });
>>>>>>> 284e251ce60564e812888c40ae43c01b7d4a7614
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
<<<<<<< HEAD
        try {
            $signatureService = new SignatureService();
            $validated['gambar_ttd'] = $signatureService->validateAndProcessSignature($request->gambar_ttd);
        } catch (\Exception $e) {
            return back()->withErrors(['gambar_ttd' => $e->getMessage()])->withInput();
=======
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
>>>>>>> 284e251ce60564e812888c40ae43c01b7d4a7614
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
<<<<<<< HEAD
        // Cache agendas for 30 minutes (form data doesn't need real-time updates)
        $agendas = Cache::remember('agendas_for_participants_form', now()->addMinutes(30), function () {
            return Agenda::orderBy('tanggal_agenda', 'asc')->get();
        });

        // Cache master dinas for 1 hour (master data changes infrequently)
        $dinas = Cache::remember('master_dinas_all', now()->addHour(), function () {
            return MasterDinas::all();
        });

=======
        // Cache daftar agenda dan dinas untuk performa
        $agendas = Cache::remember('agendas_all', 1800, function () { // Cache 30 menit
            return Agenda::select('id', 'nama_agenda', 'tanggal_agenda')->orderBy('tanggal_agenda', 'asc')->get();
        });
        $dinas = Cache::remember('master_dinas', 3600, function () { // Cache 1 jam
            return MasterDinas::all();
        });
>>>>>>> 284e251ce60564e812888c40ae43c01b7d4a7614
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
<<<<<<< HEAD
        try {
            $signatureService = new SignatureService();
            $processedSignature = $signatureService->validateAndProcessSignature($request->gambar_ttd, $participant->gambar_ttd);
            $validated['gambar_ttd'] = $processedSignature ?? $participant->gambar_ttd;
        } catch (\Exception $e) {
            return back()->withErrors(['gambar_ttd' => $e->getMessage()])->withInput();
=======
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
>>>>>>> 284e251ce60564e812888c40ae43c01b7d4a7614
        }

        $participant->update($validated);

        return redirect()->route('admin.participants.index')
            ->with('success', 'Peserta berhasil diupdate!');
    }

    public function destroy(Request $request, $id)
    {
<<<<<<< HEAD
        try {
            $participant = AgendaDetail::findOrFail($id);
=======
        // Hapus file tanda tangan jika ada dan bukan base64
        if ($participant->gambar_ttd && !str_contains($participant->gambar_ttd, 'data:image/')) {
            SignatureHelper::deleteSignature($participant->gambar_ttd);
        }

        $participant->delete();
>>>>>>> 284e251ce60564e812888c40ae43c01b7d4a7614

            // Hapus file tanda tangan jika ada
            $signatureService = new SignatureService();
            $signatureService->deleteSignature($participant->gambar_ttd);

            $participant->delete();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Peserta berhasil dihapus!'
                ]);
            }

            return redirect()->route('admin.participants.index')
                ->with('success', 'Peserta berhasil dihapus!');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Peserta tidak ditemukan atau sudah dihapus.'
                ], 404);
            }

            return redirect()->route('admin.participants.index')
                ->with('error', 'Peserta tidak ditemukan atau sudah dihapus.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menghapus peserta.'
                ], 500);
            }

            return redirect()->route('admin.participants.index')
                ->with('error', 'Terjadi kesalahan saat menghapus peserta.');
        }
    }

<<<<<<< HEAD


    /**
     * Private method untuk menerapkan filter pada query participants
     */
    private function applyParticipantFilters($query, $request)
    {
        // Filter berdasarkan tanggal, bulan, dan tahun (created_at)
        if ($request->filled('tanggal')) {
            $query->whereDay('created_at', $request->tanggal);
        }

        if ($request->filled('bulan')) {
            $query->whereMonth('created_at', $request->bulan);
        }

        if ($request->filled('tahun')) {
            $query->whereYear('created_at', $request->tahun);
        }

        if ($request->filled('agenda_id')) {
            $query->where('agenda_id', $request->agenda_id);
        }
    }

    /**
     * AJAX method untuk load participants secara dinamis
     */
    public function loadParticipants(Request $request)
    {
        try {
            $query = AgendaDetail::with(['agenda', 'masterDinas'])->orderBy('created_at', 'asc');

            $this->applyParticipantFilters($query, $request);

            $perPage = $request->get('per_page', 10);
            $participants = $query->paginate($perPage);

            // Get agenda info for filter display
            $agenda = null;
            if ($request->filled('agenda_id')) {
                $agenda = Agenda::find($request->agenda_id);
            }

            // Generate HTML for participants table
            $html = view('admin.participants.partials.participants_table', compact('participants'))->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'pagination' => [
                    'current_page' => $participants->currentPage(),
                    'last_page' => $participants->lastPage(),
                    'per_page' => $participants->perPage(),
                    'total' => $participants->total(),
                    'from' => $participants->firstItem(),
                    'to' => $participants->lastItem(),
                    'has_more_pages' => $participants->hasMorePages(),
                    'has_pages' => $participants->hasPages()
                ],
                'total' => $participants->total(),
                'current_page' => $participants->currentPage(),
                'last_page' => $participants->lastPage(),
                'has_pages' => $participants->hasPages(),
                'per_page' => $perPage,
                'agenda' => $agenda ? [
                    'id' => $agenda->id,
                    'nama_agenda' => $agenda->nama_agenda
                ] : null
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memuat data peserta',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * AJAX method untuk search agenda (untuk dropdown)
     */
    public function searchAgenda(Request $request)
    {
        try {
            $query = $request->get('q', '');
            $limit = (int) $request->get('limit', 50);

            if ($limit > 100) {
                $limit = 100;
            }

            $agendasQuery = Agenda::select([
                    'id',
                    'nama_agenda',
                    'tanggal_agenda as tanggal_mulai',
                    'link_active',
                    'dinas_id',
                    'created_at'
                ])
                ->with(['masterDinas:dinas_id,nama_dinas'])
                ->orderBy('nama_agenda', 'asc');

            if (!empty($query)) {
                $agendasQuery->where('nama_agenda', 'LIKE', '%' . $query . '%');
            }

            $agendasQuery->where('link_active', true);
            $agendas = $agendasQuery->limit($limit)->get();

            $formattedAgendas = $agendas->map(function ($agenda) {
                return [
                    'id' => $agenda->id,
                    'nama_agenda' => $agenda->nama_agenda,
                    'tanggal_mulai' => $agenda->tanggal_mulai,
                    'status' => $agenda->link_active ? 'active' : 'inactive',
                    'dinas_nama' => $agenda->masterDinas->nama_dinas ?? 'N/A',
                    'created_at' => $agenda->created_at->format('Y-m-d H:i:s'),
                    'display_text' => $agenda->nama_agenda,
                    'subtitle' => ($agenda->masterDinas->nama_dinas ?? 'N/A') .
                                  ' • ' .
                                  ($agenda->tanggal_mulai ? \Carbon\Carbon::parse($agenda->tanggal_mulai)->format('d M Y') : 'Tanggal belum ditentukan'),
                ];
            });

            return response()->json([
                'success' => true,
                'agendas' => $formattedAgendas,
                'total' => $formattedAgendas->count(),
                'query' => $query
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mencari agenda',
                'agendas' => [],
                'total' => 0
            ], 500);
        }
    }


=======
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
>>>>>>> 284e251ce60564e812888c40ae43c01b7d4a7614
}
