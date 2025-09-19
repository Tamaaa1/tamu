<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\MasterDinas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Helpers\SignatureHelper;
use App\Traits\Filterable;

/**
 * Controller untuk mengelola pendaftaran agenda secara publik
 *
 * Menangani tampilan dan proses pendaftaran peserta agenda
 * melalui interface publik tanpa autentikasi.
 *
 * @package App\Http\Controllers
 */
class PublicAgendaController extends Controller
{
    use Filterable;

    /**
     * Membuat instance controller baru
     *
     * Menerapkan rate limiting pada method registerParticipant
     * untuk mencegah spam pendaftaran (10 requests per menit)
     */
    public function __construct()
    {
        $this->middleware('throttle:10,1')->only(['registerParticipant']);
    }

    
    //Tampilan pendaftaran publik - agenda hari ini
    public function showPublic()
    {
        // Ambil agenda untuk hari ini yang aktif dengan caching
        $today = now()->format('Y-m-d');
        $cacheKey = 'active_agendas_' . $today;

        $agenda = Cache::remember($cacheKey, now()->addMinutes(30), function () use ($today) {
            return Agenda::whereDate('tanggal_agenda', $today)
                ->where('link_active', true)
                ->orderBy('nama_agenda')
                ->first();
        });

        // Jika tidak ada agenda hari ini yang aktif, tampilkan halaman no-agenda
        if (!$agenda) {
            return view('participant.no-agenda');
        }

        $dinas = Cache::remember('master_dinas', now()->addHours(1), function () {
            return MasterDinas::all();
        });

        // Ambil semua agenda pada tanggal yang sama (hari ini) yang aktif untuk dropdown pilihan
        $agendasOnSameDate = Cache::remember($cacheKey . '_list', now()->addMinutes(30), function () use ($today) {
            return Agenda::whereDate('tanggal_agenda', $today)
                ->where('link_active', true)
                ->orderBy('nama_agenda')
                ->get();
        });

        $agendas = Cache::remember('active_agendas_all', now()->addMinutes(30), function () {
            return Agenda::where('link_active', true)
                ->orderBy('nama_agenda')
                ->get();
        });

        return view('participant.public-register', compact('agenda', 'dinas', 'agendasOnSameDate', 'agendas'));
    }

    //Tampilan pendaftaran publik untuk agenda tertentu
    public function showPublicAgenda($agendaId)
    {
        // Cari agenda berdasarkan ID yang aktif, jika tidak ditemukan atau tidak aktif tampilkan halaman no-agenda
        $agenda = Agenda::where('id', $agendaId)
            ->where('link_active', true)
            ->first();

        if (!$agenda) {
            return view('participant.no-agenda');
        }

        $dinas = MasterDinas::all();

        // Ambil semua agenda pada tanggal yang sama yang aktif untuk dropdown pilihan
        $agendasOnSameDate = Agenda::whereDate('tanggal_agenda', $agenda->tanggal_agenda)
            ->where('link_active', true)
            ->orderBy('nama_agenda')
            ->get();

        $agendas = Agenda::where('link_active', true)
            ->orderBy('nama_agenda')
            ->get();

        return view('participant.public-register', compact('agenda', 'dinas', 'agendasOnSameDate', 'agendas'));
    }

    /**
     * Tampilan pendaftaran publik berdasarkan token unik
     */
    public function showPublicAgendaByToken($token)
    {
        // Cari agenda berdasarkan token unik yang aktif
        $agenda = Agenda::byToken($token)
            ->where('link_active', true)
            ->first();

        if (!$agenda) {
            return view('participant.no-agenda');
        }

        $dinas = MasterDinas::all();

        // Ambil semua agenda pada tanggal yang sama yang aktif untuk dropdown pilihan
        $agendasOnSameDate = Agenda::whereDate('tanggal_agenda', $agenda->tanggal_agenda)
            ->where('link_active', true)
            ->orderBy('nama_agenda')
            ->get();

        $agendas = Agenda::where('link_active', true)
            ->orderBy('nama_agenda')
            ->get();

        return view('participant.public-register', compact('agenda', 'dinas', 'agendasOnSameDate', 'agendas'));
    }

    /**
     * Proses pendaftaran peserta publik
     */
    public function registerParticipant(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|min:2|max:100',
            'gender' => 'required|in:Laki-laki,Perempuan',
            'jabatan' => 'required|string',
            'no_hp' => 'required|string|regex:/^[0-9]+$/|min:10|max:13',
            'dinas_id' => 'required|exists:master_dinas,dinas_id',
            'signature' => 'required|string',
            'agenda_id' => 'required|exists:agendas,id'
        ], [
            'nama.required' => 'Nama harus diisi',
            'nama.min' => 'Nama minimal 2 karakter',
            'nama.max' => 'Nama maksimal 100 karakter',
            'gender.required' => 'Jenis kelamin harus dipilih',
            'gender.in' => 'Jenis kelamin tidak valid',
            'jabatan.required' => 'Jabatan harus diisi',
            'no_hp.required' => 'Nomor HP harus diisi',
            'no_hp.regex' => 'Nomor HP hanya boleh berisi angka',
            'no_hp.min' => 'Nomor HP minimal 10 digit',
            'no_hp.max' => 'Nomor HP maksimal 13 digit',
            'dinas_id.required' => 'Dinas harus dipilih',
            'dinas_id.exists' => 'Dinas yang dipilih tidak valid',
            'signature.required' => 'Tanda tangan harus diisi'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Convert tanda tangan Base64 menjadi file menggunakan helper
        $signaturePath = null;
        if ($request->signature) {
            try {
                $signaturePath = SignatureHelper::processSignature($request->signature);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }
        }



        // Save participant data
        $participant = \App\Models\AgendaDetail::create([
            'agenda_id' => $request->agenda_id,
            'nama' => $request->nama,
            'gender' => $request->gender,
            'dinas_id' => $request->dinas_id,
            'jabatan' => $request->jabatan,
            'no_hp' => $request->no_hp,
            'gambar_ttd' => $signaturePath,
        ]);

        // Logging pendaftaran peserta
        Log::info('Peserta terdaftar: ', ['participant_id' => $participant->id, 'nama' => $participant->nama]);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disimpan!',
            'participant' => $participant
        ]);
    }
}
