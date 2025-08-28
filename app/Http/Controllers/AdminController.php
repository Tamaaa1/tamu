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

        // Filter berdasarkan tanggal, bulan, dan tahun
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

        // Batasi jumlah data untuk export PDF
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

    // User Management Methods
    public function userIndex()
    {
        $users = User::where('role', '!=', 'superadmin')->get();
        return view('admin.users.index', compact('users'));
    }

    public function userCreate()
    {
        return view('admin.users.create');
    }

    public function userStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,user'
        ]);

        $validated['password'] = bcrypt($validated['password']);

        User::create($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan!');
    }

    public function userEdit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function userUpdate(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,user'
        ]);

        if ($request->filled('password')) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil diupdate!');
    }

    public function userDestroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus!');
    }
}
