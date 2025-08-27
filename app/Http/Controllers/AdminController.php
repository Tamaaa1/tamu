<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\AgendaDetail;
use App\Models\MasterDinas;
use App\Models\User;
use App\Exports\ParticipantsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalAgendas = Agenda::count();
        $totalParticipants = AgendaDetail::count();
        $totalDinas = MasterDinas::count();
        $totalUsers = User::count();

        $recentAgendas = Agenda::with(['masterDinas', 'koordinator'])
            ->latest()
            ->take(5)
            ->get();
            
        $recentParticipants = AgendaDetail::with(['agenda', 'masterDinas'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalAgendas',
            'totalParticipants', 
            'totalDinas',
            'totalUsers',
            'recentAgendas',
            'recentParticipants'
        ));
    }

    public function exportParticipantsExcel(Request $request)
    {
        $query = AgendaDetail::with(['agenda', 'masterDinas']);

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

        // Filter berdasarkan agenda
        if ($request->filled('agenda_id')) {
            $query->where('agenda_id', $request->agenda_id);
        }

        $participants = $query->get();
        
        $filename = 'peserta_' . date('Y-m-d_H-i-s') . '.xlsx';
        return Excel::download(new ParticipantsExport($participants), $filename);
    }

    public function exportParticipantsPdf(Request $request)
    {
        $query = AgendaDetail::with(['agenda', 'masterDinas'])->orderBy('created_at', 'desc');

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

        // Filter berdasarkan agenda
        if ($request->filled('agenda_id')) {
            $query->where('agenda_id', $request->agenda_id);
        }

        // Batasi jumlah data untuk PDF export
        $participants = $query->limit(500)->get();
        
        // Konfigurasi DomPDF 
        $pdf = PDF::setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'dpi' => 96,
            'defaultFont' => 'sans-serif'
        ])->loadView('admin.participants.export-pdf', compact('participants'));
        
        $filename = 'peserta_' . date('Y-m-d_H-i-s') . '.pdf';
        return $pdf->download($filename);
    }

    public function participantIndex(Request $request)
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
        $agendas = Agenda::all(); // Tambahkan ini untuk dropdown filter
        
        return view('admin.participants.index', compact('participants', 'agendas'));
    }

    public function participantShow(AgendaDetail $participant)
    {
        $participant->load(['agenda', 'masterDinas']);
        return view('admin.participants.show', compact('participant'));
    }

    public function participantEdit(AgendaDetail $participant)
    {
        $agendas = Agenda::all();
        $dinas = MasterDinas::all();
        return view('admin.participants.edit', compact('participant', 'agendas', 'dinas'));
    }

    public function participantUpdate(Request $request, AgendaDetail $participant)
    {
        $validated = $request->validate([
            'agenda_id' => 'required|exists:agendas,id',
            'nama' => 'required|string|max:255',
            'dinas_id' => 'required|exists:master_dinas,dinas_id',
            'jabatan' => 'required|string|max:255',
            'no_hp' => 'required|string|max:20',
            'gambar_ttd' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        if ($request->hasFile('gambar_ttd')) {
            // Hapus file lama jika ada
            if ($participant->gambar_ttd && Storage::disk('public')->exists($participant->gambar_ttd)) {
                Storage::disk('public')->delete($participant->gambar_ttd);
            }
            
            $path = $request->file('gambar_ttd')->store('tandatangan', 'public');
            $validated['gambar_ttd'] = $path;
        }

        $participant->update($validated);

        return redirect()->route('admin.participants.index')
            ->with('success', 'Peserta berhasil diupdate!');
    }

    public function participantDestroy(AgendaDetail $participant)
    {
        $participant->delete();
        return redirect()->route('admin.participants.index')
            ->with('success', 'Peserta berhasil dihapus!');
    }
}
