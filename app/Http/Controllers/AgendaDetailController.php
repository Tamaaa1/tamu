<?php

namespace App\Http\Controllers;

use App\Models\AgendaDetail;
use App\Models\Agenda;
use App\Models\MasterDinas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Services\SignatureService;

class AgendaDetailController extends Controller
{
    // Menampilkan Daftar dari resource
    public function index(Request $request)
    {
        $query = AgendaDetail::with(['agenda', 'masterDinas'])->latest();

        $participants = $query->get();
        return view('admin.agenda-detail.index', compact('participants'));
    }

     // Menampilkan formulir untuk membuat resource baru.
    public function create()
    {
        // Cache agendas for 30 minutes (form data doesn't need real-time updates)
        $agendas = Cache::remember('agendas_for_participants_form', now()->addMinutes(30), function () {
            return Agenda::all();
        });

        // Cache master dinas for 1 hour (master data changes infrequently)
        $dinas = Cache::remember('master_dinas_all', now()->addHour(), function () {
            return MasterDinas::all();
        });

        return view('agenda-detail.create', compact('agendas', 'dinas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'agenda_id' => 'required|exists:agendas,id',
            'nama' => 'required|string|min:2|max:100',
            'dinas_id' => 'required|exists:master_dinas,dinas_id',
            'jabatan' => 'required|string',
            'no_hp' => 'required|string|regex:/^[0-9]+$/|min:10|max:13',
            'gambar_ttd' => 'nullable|image|mimes:png,jpg,jpeg|max:2048'
        ]);

        // Handle signature pad data (base64 image)
        try {
            $signatureService = new SignatureService();
            $validated['gambar_ttd'] = $signatureService->validateAndProcessSignature($request->gambar_ttd);
        } catch (\Exception $e) {
            return back()->withErrors(['gambar_ttd' => $e->getMessage()])->withInput();
        }

        AgendaDetail::create($validated);

        return redirect()->route('agenda-detail.index')
            ->with('success', 'Peserta berhasil ditambahkan!');
    }

    // Menampilkan resource yang ditentukan.
    public function show(AgendaDetail $agendaDetail)
    {
        $agendaDetail->load(['agenda', 'masterDinas']);
        return view('agenda-detail.show', compact('agendaDetail'));
    }

    // Menampilkan Form untuk mengedit resource yang ditentukan.
    public function edit(AgendaDetail $agendaDetail)
    {
        // Cache agendas for 30 minutes (form data doesn't need real-time updates)
        $agendas = Cache::remember('agendas_for_participants_form', now()->addMinutes(30), function () {
            return Agenda::all();
        });

        // Cache master dinas for 1 hour (master data changes infrequently)
        $dinas = Cache::remember('master_dinas_all', now()->addHour(), function () {
            return MasterDinas::all();
        });

        return view('agenda-detail.edit', compact('agendaDetail', 'agendas', 'dinas'));
    }

    // Memperbarui resource yang ditentukan di penyimpanan.
    public function update(Request $request, AgendaDetail $agendaDetail)
    {
        $validated = $request->validate([
            'agenda_id' => 'required|exists:agendas,id',
            'nama' => 'required|string|min:2|max:100',
            'dinas_id' => 'required|exists:master_dinas,dinas_id',
            'jabatan' => 'required|string',
            'no_hp' => 'required|string|regex:/^[0-9]+$/|min:10|max:13',
            'gambar_ttd' => 'nullable|image|mimes:png,jpg,jpeg|max:2048'
        ]);

        // Handle signature pad data (base64 image)
        try {
            $signatureService = new SignatureService();
            $processedSignature = $signatureService->validateAndProcessSignature($request->gambar_ttd, $agendaDetail->gambar_ttd);
            $validated['gambar_ttd'] = $processedSignature ?? $agendaDetail->gambar_ttd;
        } catch (\Exception $e) {
            return back()->withErrors(['gambar_ttd' => $e->getMessage()])->withInput();
        }

        $agendaDetail->update($validated);

        return redirect()->route('agenda-detail.index')
            ->with('success', 'Data peserta berhasil diupdate!');
    }

    // Menghapus resource yang ditentukan dari penyimpanan.
    public function destroy(AgendaDetail $agendaDetail)
    {
        // Hapus file tanda tangan jika ada
        $signatureService = new SignatureService();
        $signatureService->deleteSignature($agendaDetail->gambar_ttd);

        $agendaDetail->delete();

        return redirect()->route('agenda-detail.index')
            ->with('success', 'Peserta berhasil dihapus!');
    }
}
