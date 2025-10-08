<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\AgendaDetail;
use App\Models\MasterDinas;
use App\Services\SignatureService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;


class ParticipantController extends Controller
{
    public function index(Request $request)
    {
        $query = AgendaDetail::with(['agenda', 'masterDinas'])->orderBy('created_at', 'asc');

        $this->applyParticipantFilters($query, $request);

        $participants = $query->paginate(10);
        $agendas = Agenda::orderBy('tanggal_agenda', 'asc')->get();

        return view('admin.participants.index', compact('participants', 'agendas'));
    }

    public function create()
    {
        // Cache agendas for 30 minutes (form data doesn't need real-time updates)
        $agendas = Cache::remember('agendas_for_participants_form', now()->addMinutes(30), function () {
            return Agenda::orderBy('tanggal_agenda', 'asc')->get();
        });

        // Cache master dinas for 1 hour (master data changes infrequently)
        $dinas = Cache::remember('master_dinas_all', now()->addHour(), function () {
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
        try {
            $signatureService = new SignatureService();
            $validated['gambar_ttd'] = $signatureService->validateAndProcessSignature($request->gambar_ttd);
        } catch (\Exception $e) {
            return back()->withErrors(['gambar_ttd' => $e->getMessage()])->withInput();
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
        // Cache agendas for 30 minutes (form data doesn't need real-time updates)
        $agendas = Cache::remember('agendas_for_participants_form', now()->addMinutes(30), function () {
            return Agenda::orderBy('tanggal_agenda', 'asc')->get();
        });

        // Cache master dinas for 1 hour (master data changes infrequently)
        $dinas = Cache::remember('master_dinas_all', now()->addHour(), function () {
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
        try {
            $signatureService = new SignatureService();
            $processedSignature = $signatureService->validateAndProcessSignature($request->gambar_ttd, $participant->gambar_ttd);
            $validated['gambar_ttd'] = $processedSignature ?? $participant->gambar_ttd;
        } catch (\Exception $e) {
            return back()->withErrors(['gambar_ttd' => $e->getMessage()])->withInput();
        }

        $participant->update($validated);

        return redirect()->route('admin.participants.index')
            ->with('success', 'Peserta berhasil diupdate!');
    }

    public function destroy(Request $request, $id)
    {
        try {
            $participant = AgendaDetail::findOrFail($id);

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


}
