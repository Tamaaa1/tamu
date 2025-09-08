<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\AgendaDetail;
use App\Models\MasterDinas;
use App\Models\User;
use App\Exports\ParticipantsExport;
use App\Traits\Filterable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class AdminController extends Controller
{
    use Filterable;
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

        // Apply filters using trait
        $query = $this->applyDateFilters($query, $request);
        $query = $this->applyAgendaFilter($query, $request);

        $participants = $query->get();

        $filename = 'peserta_' . date('Y-m-d_H-i-s') . '.xlsx';
        return Excel::download(new ParticipantsExport($participants), $filename);
    }

    public function exportParticipantsPdf(Request $request)
    {
        $query = AgendaDetail::with(['agenda', 'masterDinas'])->orderBy('created_at', 'desc');

        // Apply filters using trait
        $query = $this->applyDateFilters($query, $request);
        $query = $this->applyAgendaFilter($query, $request);

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
        // Validasi input pengguna baru dengan aturan keamanan
        $validated = $request->validate([
            'nama' => 'required|string|max:255', // Nama lengkap pengguna
            'nama_pengguna' => 'required|string|unique:users,username', // Username unik
            'kata_sandi' => 'required|string|min:8|confirmed', // Password dengan konfirmasi
            'peran' => 'required|in:admin,pengguna' // Role pengguna (admin atau pengguna biasa)
        ]);

        // Hash password untuk keamanan sebelum disimpan ke database
        $validated['password'] = Hash::make($validated['kata_sandi']);
        // Hapus field kata_sandi karena sudah di-hash ke password
        unset($validated['kata_sandi']);

        // Simpan pengguna baru ke database
        User::create($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berhasil ditambahkan!');
    }

    public function userEdit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function userUpdate(Request $request, User $user)
    {
        // Validasi input update pengguna dengan pengecualian ID pengguna saat ini
        $validated = $request->validate([
            'nama' => 'required|string|max:255', // Nama lengkap pengguna
            'nama_pengguna' => 'required|string|unique:users,username,' . $user->id, // Username unik (exclude current user)
            'kata_sandi' => 'nullable|string|min:8|confirmed', // Password opsional dengan konfirmasi
            'peran' => 'required|in:admin,pengguna' // Role pengguna
        ]);

        // Jika password diisi, hash password baru untuk keamanan
        if ($request->filled('kata_sandi')) {
            $validated['password'] = Hash::make($validated['kata_sandi']);
            // Hapus field kata_sandi karena sudah di-hash
            unset($validated['kata_sandi']);
        } else {
            // Jika password tidak diubah, hapus dari array validasi
            unset($validated['kata_sandi']);
            unset($validated['password']);
        }

        // Update data pengguna di database
        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berhasil diupdate!');
    }

    public function userDestroy(User $user)
    {
        // Hapus pengguna dari database
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berhasil dihapus!');
    }
}
