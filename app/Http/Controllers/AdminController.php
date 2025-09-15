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

    public function exportParticipantsPdf(Request $request)
    {
        $query = AgendaDetail::with(['agenda', 'masterDinas'])->orderBy('created_at', 'desc');

        // Apply filters using trait
        $query = $this->applyDateFilters($query, $request);
        $query = $this->applyAgendaFilter($query, $request);

        // Batasi jumlah data untuk export PDF
        $participants = $query->limit(500)->get();

        // Get agenda filter if applied
        $agendaFilter = null;
        if ($request->filled('agenda_id')) {
            $agendaFilter = Agenda::find($request->agenda_id);
        }

        // Konfigurasi DomPDF
        $pdf = PDF::setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'dpi' => 96,
            'defaultFont' => 'sans-serif'
        ])->loadView('admin.participants.export-pdf', compact('participants', 'agendaFilter'));

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
            'name' => 'required|string|max:255', // Nama lengkap pengguna
            'username' => 'required|string|unique:users,username', // Username unik
            'password' => 'required|string|min:8|confirmed|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/', // Password dengan konfirmasi dan validasi kompleksitas
            'role' => 'required|in:admin,user' // Role pengguna (admin atau user biasa)
        ], [
            'password.regex' => 'Password harus mengandung minimal 1 huruf kecil, 1 huruf besar, dan 1 angka.',
            'password.*' => 'Password tidak memenuhi syarat keamanan.'
        ]);

        // Hash password untuk keamanan sebelum disimpan ke database
        $validated['password'] = Hash::make($validated['password']);

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
            'name' => 'required|string|max:255', // Nama lengkap pengguna
            'username' => 'required|string|unique:users,username,' . $user->id, // Username unik (exclude current user)
            'password' => 'nullable|string|min:8|confirmed|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/', // Password opsional dengan konfirmasi dan validasi kompleksitas
            'role' => 'required|in:admin,user' // Role pengguna
        ], [
            'password.regex' => 'Password harus mengandung minimal 1 huruf kecil, 1 huruf besar, dan 1 angka.',
            'password.*' => 'Password tidak memenuhi syarat keamanan.'
        ]);

        // Jika password diisi, hash password baru untuk keamanan
        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            // Jika password tidak diubah, hapus dari array validasi
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

    /**
     * Serve signature file dengan autentikasi
     * Hanya admin yang login yang bisa mengakses file tanda tangan
     */
    public function serveSignature($filename)
    {
        // Handle filename yang mungkin sudah include "tandatangan/" prefix
        if (str_starts_with($filename, 'tandatangan/')) {
            // Jika filename sudah include prefix, gunakan langsung
            $path = $filename;
            $cleanFilename = basename($filename);
        } else {
            // Jika belum ada prefix, tambahkan
            $path = 'tandatangan/' . $filename;
            $cleanFilename = $filename;
        }

        // Cek apakah file ada di disk private
        if (!Storage::disk('local')->exists($path)) {
            abort(404, 'File tanda tangan tidak ditemukan');
        }

        // Ambil file dari disk private
        $file = Storage::disk('local')->get($path);

        // Tentukan MIME type berdasarkan ekstensi file
        $mimeType = 'image/png'; // Default untuk PNG
        if (str_ends_with(strtolower($cleanFilename), '.jpg') || str_ends_with(strtolower($cleanFilename), '.jpeg')) {
            $mimeType = 'image/jpeg';
        }

        // Return file dengan header yang tepat
        return response($file)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', 'inline; filename="' . $cleanFilename . '"')
            ->header('Cache-Control', 'private, max-age=3600'); // Cache selama 1 jam
    }
}
