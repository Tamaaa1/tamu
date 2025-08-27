<?php

namespace App\Http\Controllers;

use App\Models\AgendaDetail;
use App\Models\Agenda;
use App\Models\MasterDinas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AgendaDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = AgendaDetail::with(['agenda', 'masterDinas'])->latest();

        // Filter berdasarkan tanggal, bulan, dan tahun
        if ($request->filled('tanggal')) {
            $query->whereHas('agenda', function($q) use ($request) {
                $q->whereDay('tanggal_agenda', $request->tanggal);
            });
        }
        
        if ($request->filled('bulan')) {
            $query->whereHas('agenda', function($q) use ($request) {
                $q->whereMonth('tanggal_agenda', $request->bulan);
            });
        }
        
        if ($request->filled('tahun')) {
            $query->whereHas('agenda', function($q) use ($request) {
                $q->whereYear('tanggal_agenda', $request->tahun);
            });
        }

        $participants = $query->get();
        return view('admin.agenda-detail.index', compact('participants'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $agendas = Agenda::all();
        $dinas = MasterDinas::all();
        return view('agenda-detail.create', compact('agendas', 'dinas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'agenda_id' => 'required|exists:agendas,id',
            'nama' => 'required|string|min:2|max:100',
            'dinas_id' => 'required|exists:master_dinas,dinas_id',
            'jabatan' => 'required|string',
            'no_hp' => 'required|string|regex:/^[0-9]+$/|min:10|max:13',
            'gambar_ttd' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'qr_code' => 'nullable|string'
        ]);

        if ($request->hasFile('gambar_ttd')) {
            $path = $request->file('gambar_ttd')->store('tandatangan', 'public');
            $validated['gambar_ttd'] = $path;
        }

        AgendaDetail::create($validated);

        return redirect()->route('agenda-detail.index')
            ->with('success', 'Peserta berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(AgendaDetail $agendaDetail)
    {
        $agendaDetail->load(['agenda', 'masterDinas']);
        return view('agenda-detail.show', compact('agendaDetail'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AgendaDetail $agendaDetail)
    {
        $agendas = Agenda::all();
        $dinas = MasterDinas::all();
        return view('agenda-detail.edit', compact('agendaDetail', 'agendas', 'dinas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AgendaDetail $agendaDetail)
    {
        $validated = $request->validate([
            'agenda_id' => 'required|exists:agendas,id',
            'nama' => 'required|string|min:2|max:100',
            'dinas_id' => 'required|exists:master_dinas,dinas_id',
            'jabatan' => 'required|string',
            'no_hp' => 'required|string|regex:/^[0-9]+$/|min:10|max:13',
            'gambar_ttd' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'qr_code' => 'nullable|string'
        ]);

        if ($request->hasFile('gambar_ttd')) {
            // Hapus file lama jika ada
            if ($agendaDetail->gambar_ttd && Storage::disk('public')->exists($agendaDetail->gambar_ttd)) {
                Storage::disk('public')->delete($agendaDetail->gambar_ttd);
            }
            
            $path = $request->file('gambar_ttd')->store('tandatangan', 'public');
            $validated['gambar_ttd'] = $path;
        }

        $agendaDetail->update($validated);

        return redirect()->route('agenda-detail.index')
            ->with('success', 'Data peserta berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AgendaDetail $agendaDetail)
    {
        $agendaDetail->delete();

        return redirect()->route('agenda-detail.index')
            ->with('success', 'Peserta berhasil dihapus!');
    }
}
