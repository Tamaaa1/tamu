<?php

namespace App\Http\Controllers;

use App\Models\AgendaDetail;
use App\Models\Agenda;
use App\Models\MasterDinas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Helpers\SignatureHelper;

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
        return view('admin.agenda-detail.index', compact('participants'));
    }

     // Menampilkan formulir untuk membuat resource baru.
    public function create()
    {
        $agendas = Cache::remember('agendas_all', 1800, function () { // Cache 30 menit
            return Agenda::select('id', 'nama_agenda', 'tanggal_agenda')->get();
        });
        $dinas = Cache::remember('master_dinas', 3600, function () { // Cache 1 jam
            return MasterDinas::all();
        });
        return view('admin.agenda-detail.create', compact('agendas', 'dinas'));
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
        $agendas = Cache::remember('agendas_all', 1800, function () { // Cache 30 menit
            return Agenda::select('id', 'nama_agenda', 'tanggal_agenda')->get();
        });
        $dinas = Cache::remember('master_dinas', 3600, function () { // Cache 1 jam
            return MasterDinas::all();
        });
        return view('admin.agenda-detail.edit', compact('agendaDetail', 'agendas', 'dinas'));
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
        }

        $agendaDetail->update($validated);

        return redirect()->route('agenda-detail.index')
            ->with('success', 'Data peserta berhasil diupdate!');
    }

    // Menghapus resource yang ditentukan dari penyimpanan.
    public function destroy(AgendaDetail $agendaDetail)
    {
        // Hapus file tanda tangan jika ada dan bukan base64
        if ($agendaDetail->gambar_ttd && !str_contains($agendaDetail->gambar_ttd, 'data:image/')) {
            SignatureHelper::deleteSignature($agendaDetail->gambar_ttd);
        }

        $agendaDetail->delete();

        return redirect()->route('agenda-detail.index')
            ->with('success', 'Peserta berhasil dihapus!');
    }
}
