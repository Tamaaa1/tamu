<?php

namespace App\Http\Controllers;

use App\Models\AgendaDetail;
use App\Models\Agenda;
use App\Models\MasterDinas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
<<<<<<< HEAD
use App\Services\SignatureService;
=======
use App\Helpers\SignatureHelper;
>>>>>>> 284e251ce60564e812888c40ae43c01b7d4a7614

/**
 * Controller untuk mengelola detail peserta agenda
 *
 * Menangani operasi CRUD peserta agenda, upload tanda tangan digital,
 * dan manajemen data peserta dengan fitur filter dan pencarian.
 *
 * @package App\Http\Controllers
 */
class AgendaDetailController extends Controller
{
    /**
     * Menampilkan daftar peserta agenda dengan filter
     *
     * Mengambil data peserta agenda beserta relasi agenda dan master dinas,
     * menerapkan filter berdasarkan tanggal agenda, dan menampilkan dengan pagination.
     *
     * @param Request $request Request object dengan parameter filter tanggal
     * @return \Illuminate\View\View View daftar peserta agenda
     */
    public function index(Request $request)
    {
        $query = AgendaDetail::with([
            'agenda' => function($q) use ($request) {
                // Eager loading dengan constraints untuk optimasi
                if ($request->filled('tanggal')) {
                    $q->whereDay('tanggal_agenda', $request->tanggal);
                }
                if ($request->filled('bulan')) {
                    $q->whereMonth('tanggal_agenda', $request->bulan);
                }
                if ($request->filled('tahun')) {
                    $q->whereYear('tanggal_agenda', $request->tahun);
                }
            },
            'masterDinas'
        ])->latest();

<<<<<<< HEAD
        $participants = $query->get();
=======
        // Filter berdasarkan agenda yang sudah difilter di eager loading
        if ($request->filled('tanggal') || $request->filled('bulan') || $request->filled('tahun')) {
            $query->whereHas('agenda', function($q) use ($request) {
                if ($request->filled('tanggal')) {
                    $q->whereDay('tanggal_agenda', $request->tanggal);
                }
                if ($request->filled('bulan')) {
                    $q->whereMonth('tanggal_agenda', $request->bulan);
                }
                if ($request->filled('tahun')) {
                    $q->whereYear('tanggal_agenda', $request->tahun);
                }
            });
        }

        $participants = $query->paginate(15); // Menggunakan pagination untuk performa
>>>>>>> 284e251ce60564e812888c40ae43c01b7d4a7614
        return view('admin.agenda-detail.index', compact('participants'));
    }

     // Menampilkan formulir untuk membuat resource baru.
    public function create()
    {
<<<<<<< HEAD
        // Cache agendas for 30 minutes (form data doesn't need real-time updates)
        $agendas = Cache::remember('agendas_for_participants_form', now()->addMinutes(30), function () {
            return Agenda::all();
        });

        // Cache master dinas for 1 hour (master data changes infrequently)
        $dinas = Cache::remember('master_dinas_all', now()->addHour(), function () {
            return MasterDinas::all();
        });

        return view('agenda-detail.create', compact('agendas', 'dinas'));
=======
        $agendas = Cache::remember('agendas_all', 1800, function () { // Cache 30 menit
            return Agenda::select('id', 'nama_agenda', 'tanggal_agenda')->get();
        });
        $dinas = Cache::remember('master_dinas', 3600, function () { // Cache 1 jam
            return MasterDinas::all();
        });
        return view('admin.agenda-detail.create', compact('agendas', 'dinas'));
>>>>>>> 284e251ce60564e812888c40ae43c01b7d4a7614
    }

    /**
     * Menyimpan peserta agenda baru ke database
     *
     * Melakukan validasi input, memproses tanda tangan digital base64,
     * dan menyimpan data peserta agenda baru.
     *
     * @param Request $request Data request dari form pendaftaran peserta
     * @return \Illuminate\Http\RedirectResponse Redirect ke index dengan pesan sukses atau error
     */
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

<<<<<<< HEAD
        // Handle signature pad data (base64 image)
        try {
            $signatureService = new SignatureService();
            $validated['gambar_ttd'] = $signatureService->validateAndProcessSignature($request->gambar_ttd);
        } catch (\Exception $e) {
            return back()->withErrors(['gambar_ttd' => $e->getMessage()])->withInput();
=======
        if ($request->filled('gambar_ttd') && strpos($request->gambar_ttd, 'data:image/') === 0) {
            // Simpan tanda tangan menggunakan SignatureHelper
            try {
                $signaturePath = SignatureHelper::processSignature($request->gambar_ttd);
                $validated['gambar_ttd'] = $signaturePath;
            } catch (\Exception $e) {
                return back()->withErrors(['gambar_ttd' => 'Gagal menyimpan tanda tangan: ' . $e->getMessage()])->withInput();
            }
        } else {
            $validated['gambar_ttd'] = null;
>>>>>>> 284e251ce60564e812888c40ae43c01b7d4a7614
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
<<<<<<< HEAD
        // Cache agendas for 30 minutes (form data doesn't need real-time updates)
        $agendas = Cache::remember('agendas_for_participants_form', now()->addMinutes(30), function () {
            return Agenda::all();
        });

        // Cache master dinas for 1 hour (master data changes infrequently)
        $dinas = Cache::remember('master_dinas_all', now()->addHour(), function () {
            return MasterDinas::all();
        });

        return view('agenda-detail.edit', compact('agendaDetail', 'agendas', 'dinas'));
=======
        $agendas = Cache::remember('agendas_all', 1800, function () { // Cache 30 menit
            return Agenda::select('id', 'nama_agenda', 'tanggal_agenda')->get();
        });
        $dinas = Cache::remember('master_dinas', 3600, function () { // Cache 1 jam
            return MasterDinas::all();
        });
        return view('admin.agenda-detail.edit', compact('agendaDetail', 'agendas', 'dinas'));
>>>>>>> 284e251ce60564e812888c40ae43c01b7d4a7614
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

<<<<<<< HEAD
        // Handle signature pad data (base64 image)
        try {
            $signatureService = new SignatureService();
            $processedSignature = $signatureService->validateAndProcessSignature($request->gambar_ttd, $agendaDetail->gambar_ttd);
            $validated['gambar_ttd'] = $processedSignature ?? $agendaDetail->gambar_ttd;
        } catch (\Exception $e) {
            return back()->withErrors(['gambar_ttd' => $e->getMessage()])->withInput();
=======
        if ($request->filled('gambar_ttd') && strpos($request->gambar_ttd, 'data:image/') === 0) {
            // Hapus tanda tangan lama jika ada
            if ($agendaDetail->gambar_ttd && !str_contains($agendaDetail->gambar_ttd, 'data:image/')) {
                SignatureHelper::deleteSignature($agendaDetail->gambar_ttd);
            }
            // Simpan tanda tangan baru
            try {
                $signaturePath = SignatureHelper::processSignature($request->gambar_ttd);
                $validated['gambar_ttd'] = $signaturePath;
            } catch (\Exception $e) {
                return back()->withErrors(['gambar_ttd' => 'Gagal menyimpan tanda tangan: ' . $e->getMessage()])->withInput();
            }
        } else {
            $validated['gambar_ttd'] = $agendaDetail->gambar_ttd;
>>>>>>> 284e251ce60564e812888c40ae43c01b7d4a7614
        }

        $agendaDetail->update($validated);

        return redirect()->route('agenda-detail.index')
            ->with('success', 'Data peserta berhasil diupdate!');
    }

    // Menghapus resource yang ditentukan dari penyimpanan.
    public function destroy(AgendaDetail $agendaDetail)
    {
<<<<<<< HEAD
        // Hapus file tanda tangan jika ada
        $signatureService = new SignatureService();
        $signatureService->deleteSignature($agendaDetail->gambar_ttd);
=======
        // Hapus file tanda tangan jika ada dan bukan base64
        if ($agendaDetail->gambar_ttd && !str_contains($agendaDetail->gambar_ttd, 'data:image/')) {
            SignatureHelper::deleteSignature($agendaDetail->gambar_ttd);
        }
>>>>>>> 284e251ce60564e812888c40ae43c01b7d4a7614

        $agendaDetail->delete();

        return redirect()->route('agenda-detail.index')
            ->with('success', 'Peserta berhasil dihapus!');
    }
}
