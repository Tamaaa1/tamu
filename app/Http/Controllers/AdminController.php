<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\AgendaDetail;
use App\Models\User;
use App\Services\ParticipantsExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
<<<<<<< HEAD
=======
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
>>>>>>> 284e251ce60564e812888c40ae43c01b7d4a7614

class AdminController extends Controller
{
    public function dashboard()
    {
        // Cache agenda counts for 10 minutes (dashboard data doesn't need real-time updates)
        $cacheKeyPrefix = 'dashboard_counts_' . date('Y-m-d-H');
        $agendasToday = Cache::remember($cacheKeyPrefix . '_today', now()->addMinutes(10), function () {
            return Agenda::today()->count();
        });
        $agendasThisWeek = Cache::remember($cacheKeyPrefix . '_week', now()->addMinutes(10), function () {
            return Agenda::thisWeek()->count();
        });
        $agendasThisMonth = Cache::remember($cacheKeyPrefix . '_month', now()->addMinutes(10), function () {
            return Agenda::thisMonth()->count();
        });
        $agendasThisYear = Cache::remember($cacheKeyPrefix . '_year', now()->addMinutes(10), function () {
            return Agenda::thisYear()->count();
        });

        // Cache recent agendas for 5 minutes
        $recentAgendas = Cache::remember('dashboard_recent_agendas', now()->addMinutes(5), function () {
            return Agenda::with(['masterDinas', 'koordinator'])
                ->latest()
                ->take(5)
                ->get();
        });

        // Cache recent participants for 2 minutes (more frequent updates needed)
        $recentParticipants = Cache::remember('dashboard_recent_participants', now()->addMinutes(2), function () {
            return AgendaDetail::with(['agenda', 'masterDinas'])
                ->latest()
                ->take(5)
                ->get();
        });

        return view('admin.dashboard', compact(
            'agendasToday',
            'agendasThisWeek',
            'agendasThisMonth',
            'agendasThisYear',
            'recentAgendas',
            'recentParticipants'
        ));
    }

<<<<<<< HEAD
    /**
     * Export participants data to PDF
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportParticipantsPdf(Request $request)
    {
        $exportService = new ParticipantsExportService();
        return $exportService->exportPdf($request);
=======
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
>>>>>>> 284e251ce60564e812888c40ae43c01b7d4a7614
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
