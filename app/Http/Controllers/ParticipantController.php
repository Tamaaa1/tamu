<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\AgendaDetail;
use App\Models\MasterDinas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ParticipantController extends Controller
{
    public function index(Request $request)
    {
        $query = AgendaDetail::with(['agenda', 'masterDinas'])->orderBy('created_at', 'asc');

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

        $participants = $query->paginate(10);
        $agendas = Agenda::all(); 
        
        return view('admin.participants.index', compact('participants', 'agendas'));
    }

    public function create()
    {
        $agendas = Agenda::all();
        $dinas = MasterDinas::all();
        return view('admin.participants.create', compact('agendas', 'dinas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'agenda_id' => 'required|exists:agendas,id',
            'nama' => 'required|string|max:255',
            'dinas_id' => 'required|exists:master_dinas,dinas_id',
            'jabatan' => 'required|string|max:255',
            'no_hp' => 'required|string|max:20',
            'gambar_ttd' => 'nullable',
        ]);

        // Handle signature pad data (base64 image)
        if ($request->filled('gambar_ttd') && strpos($request->gambar_ttd, 'data:image/') === 0) {
            $validated['gambar_ttd'] = $request->gambar_ttd; // Store as base64 string
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
        $agendas = Agenda::all();
        $dinas = MasterDinas::all();
        return view('admin.participants.edit', compact('participant', 'agendas', 'dinas'));
    }

    public function update(Request $request, AgendaDetail $participant)
    {
        $validated = $request->validate([
            'agenda_id' => 'required|exists:agendas,id',
            'nama' => 'required|string|max:255',
            'dinas_id' => 'required|exists:master_dinas,dinas_id',
            'jabatan' => 'required|string|max:255',
            'no_hp' => 'required|string|max:20',
            'gambar_ttd' => 'nullable',
        ]);

        // Handle signature pad data (base64 image)
        if ($request->filled('gambar_ttd') && strpos($request->gambar_ttd, 'data:image/') === 0) {
            $validated['gambar_ttd'] = $request->gambar_ttd; // Store as base64 string
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
        $participant->delete();

        return redirect()->route('admin.participants.index')
            ->with('success', 'Peserta berhasil dihapus!');
    }
}
