<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\MasterDinas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
<<<<<<< HEAD
use App\Services\SignatureService;
=======
use App\Helpers\SignatureHelper;
>>>>>>> 284e251ce60564e812888c40ae43c01b7d4a7614
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
        // Honeypot check - bot akan mengisi field ini
        if (!empty($request->input('website')) || !empty($request->input('confirm_email'))) {
            Log::warning('Spam attempt detected via honeypot', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'data' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Permintaan tidak valid.'
            ], 400);
        }

        // Validasi reCAPTCHA jika diaktifkan
        if (config('services.recaptcha.site_key')) {
            $recaptchaResponse = $request->input('g-recaptcha-response');
            if (!$recaptchaResponse) {
                return response()->json([
                    'success' => false,
                    'errors' => ['recaptcha' => ['Verifikasi reCAPTCHA diperlukan']]
                ], 422);
            }

            // Verifikasi reCAPTCHA
            $recaptchaSecret = config('services.recaptcha.secret_key');
            $recaptchaVerify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$recaptchaSecret}&response={$recaptchaResponse}");
            $recaptchaData = json_decode($recaptchaVerify);

            if (!$recaptchaData->success || $recaptchaData->score < 0.5) {
                Log::warning('reCAPTCHA verification failed', [
                    'ip' => $request->ip(),
                    'score' => $recaptchaData->score ?? 'N/A'
                ]);
                return response()->json([
                    'success' => false,
                    'errors' => ['recaptcha' => ['Verifikasi reCAPTCHA gagal']]
                ], 422);
            }
        }

        // Validasi waktu pengisian form (minimal 3 detik untuk mencegah bot)
        $formStartTime = $request->input('form_start_time');
        if ($formStartTime && (time() - $formStartTime) < 3) {
            Log::warning('Form submitted too quickly - possible bot', [
                'ip' => $request->ip(),
                'time_taken' => time() - $formStartTime
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Form diisi terlalu cepat. Silakan coba lagi.'
            ], 429);
        }

        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|min:2|max:100',
            'gender' => 'required|in:Laki-laki,Perempuan',
            'jabatan' => 'required|string',
            'no_hp' => 'required|string|regex:/^[0-9]+$/|min:10|max:13',
            'dinas_id' => 'required|string',
            'manual_dinas' => 'nullable|string|min:2|max:255',
            'signature' => 'required|string',
            'agenda_id' => 'required|exists:agendas,id',
            'form_start_time' => 'nullable|integer'
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
            'signature.required' => 'Tanda tangan harus diisi',
            'manual_dinas.min' => 'Nama instansi minimal 2 karakter',
            'manual_dinas.max' => 'Nama instansi maksimal 255 karakter',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Handle signature pad data (base64 image)
        try {
            $signatureService = new SignatureService();
            $signaturePath = $signatureService->validateAndProcessSignature($request->signature);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }

<<<<<<< HEAD
        // Determine dinas_id to use
        $finalDinasId = $request->dinas_id;
        if ($request->dinas_id === 'other') {
            $manualDinasName = trim($request->manual_dinas);
            if (empty($manualDinasName)) {
                return response()->json([
                    'success' => false,
                    'errors' => ['manual_dinas' => ['Nama instansi harus diisi jika memilih Lainnya']]
                ], 422);
            }

            // Check if manual dinas already exists
            $existingDinas = MasterDinas::where('nama_dinas', $manualDinasName)->first();
            if ($existingDinas) {
                $finalDinasId = $existingDinas->dinas_id;
            } else {
                // Create new MasterDinas entry
                $newDinas = MasterDinas::create([
                    'dinas_id' => \Illuminate\Support\Str::uuid()->toString(),
                    'nama_dinas' => $manualDinasName,
                    'email' => null,
                    'alamat' => null,
                ]);
                $finalDinasId = $newDinas->dinas_id;

                // Clear cache for master_dinas if caching is used
                Cache::forget('master_dinas');
            }
        } else {
            // Validate that the selected dinas_id exists
            $existingDinas = MasterDinas::where('dinas_id', $request->dinas_id)->first();
            if (!$existingDinas) {
                return response()->json([
                    'success' => false,
                    'errors' => ['dinas_id' => ['Instansi yang dipilih tidak valid']]
                ], 422);
            }
            $finalDinasId = $request->dinas_id;
        }

=======


>>>>>>> 284e251ce60564e812888c40ae43c01b7d4a7614
        // Save participant data
        $participant = \App\Models\AgendaDetail::create([
            'agenda_id' => $request->agenda_id,
            'nama' => $request->nama,
            'gender' => $request->gender,
<<<<<<< HEAD
            'dinas_id' => $finalDinasId,
=======
            'dinas_id' => $request->dinas_id,
>>>>>>> 284e251ce60564e812888c40ae43c01b7d4a7614
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
