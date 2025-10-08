<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\AgendaDetail;
use App\Models\MasterDinas;
use App\Traits\Filterable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use SimpleSoftwareIO\QrCode\Facades\QrCode;


/**
 * Controller untuk mengelola agenda rapat/acara
 *
 * Menangani operasi CRUD agenda, QR code generation,
 * dan manajemen status aktif/nonaktif agenda.
 *
 * @package App\Http\Controllers
 */
class AgendaController extends Controller
{
    use Filterable;

    /**
     * Membuat instance controller baru
     *
     * Menerapkan middleware autentikasi untuk semua method
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Menampilkan daftar agenda dengan filter tanggal
     *
     * Mengambil data agenda beserta relasi master dinas dan koordinator,
     * menerapkan filter tanggal, dan menampilkan dengan pagination.
     *
     * @param Request $request Request object yang berisi parameter filter
     * @return \Illuminate\View\View View daftar agenda
     */
    public function index(Request $request)
    {
        // Membuat query untuk mengambil data agenda beserta relasi master dinas dan koordinator
        $query = Agenda::with(['masterDinas', 'koordinator'])->latest();

        // Menerapkan filter tanggal menggunakan trait Filterable
        $query = $this->applyDateFilters($query, $request, 'tanggal_agenda');

        // Mengambil data agenda dengan pagination 15 data per halaman
        $agendas = $query->paginate(15);

        // Mengembalikan view dengan data agenda
        return view('admin.agenda.index', compact('agendas'));
    }

    /**
     * Menampilkan form untuk membuat agenda baru
     *
     * Mengambil data master dinas dari cache dan menampilkan form create agenda.
     *
     * @return \Illuminate\View\View View form create agenda
     */
    public function create()
    {
        // Mengambil semua data master dinas untuk dropdown pada form dengan caching
        $dinas = Cache::remember('master_dinas', 3600, function () { // Cache 1 jam
            return MasterDinas::all();
        });

        // Mengembalikan view form tambah agenda dengan data master dinas
        return view('admin.agenda.create', compact('dinas'));
    }

    /**
     * Menyimpan agenda baru ke database
     *
     * Melakukan validasi input, mengatur koordinator otomatis,
     * dan membuat agenda baru dengan cache invalidation.
     *
     * @param Request $request Data request dari form
     * @return \Illuminate\Http\RedirectResponse Redirect ke index dengan pesan sukses
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'dinas_id' => 'required|exists:master_dinas,dinas_id',
            'nama_agenda' => 'required|string|max:255',
            'tanggal_agenda' => 'required|date',
            'tempat' => 'required|string|max:255',
            'waktu' => 'required|string|max:255',
            'link_acara' => 'nullable|string'
        ]);

        $validated['nama_koordinator'] = Auth::user()->name ?? Auth::user()->username; // Set koordinator otomatis
        $validated['link_active'] = $request->has('link_active') ? true : false; // Status aktif/nonaktif

        Agenda::create($validated);

        // Clear cache agendas setelah create
        Cache::forget('agendas_all');

        return redirect()->route('admin.agenda.index')
            ->with('success', 'Agenda berhasil dibuat!');
    }

    public function show(Agenda $agenda)
    {
        $agenda->load(['masterDinas', 'koordinator']);
        $participants = $agenda->agendaDetail()->with('masterDinas')->paginate(10);
        return view('admin.agenda.show', compact('agenda', 'participants'));
    }

    public function edit(Agenda $agenda)
    {
        $dinas = Cache::remember('master_dinas', 3600, function () { // Cache 1 jam
            return MasterDinas::all();
        });
        return view('admin.agenda.edit', compact('agenda', 'dinas'));
    }

    public function update(Request $request, Agenda $agenda)
    {
        $validated = $request->validate([
            'dinas_id' => 'required|exists:master_dinas,dinas_id',
            'nama_agenda' => 'required|string|max:255',
            'tanggal_agenda' => 'required|date',
            'tempat' => 'required|string|max:255',
            'waktu' => 'required|string|max:255',
            'link_acara' => 'nullable|string'
        ]);

        $validated['link_active'] = $request->has('link_active') ? true : false;

        $agenda->update($validated);

        // Clear cache agendas setelah update
        Cache::forget('agendas_all');

        return redirect()->route('admin.agenda.index')
            ->with('success', 'Agenda berhasil diupdate!');
    }

    public function destroy(Request $request, $id)
    {
        try {
            $agenda = Agenda::findOrFail($id);
            $agenda->delete();

<<<<<<< HEAD
            // Check if this is an AJAX request
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Agenda berhasil dihapus!'
                ]);
            }

            return redirect()->route('admin.agenda.index')
                ->with('success', 'Agenda berhasil dihapus!');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Agenda not found (already deleted or never existed)
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Agenda tidak ditemukan atau sudah dihapus.'
                ], 404);
            }

            return redirect()->route('admin.agenda.index')
                ->with('error', 'Agenda tidak ditemukan atau sudah dihapus.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menghapus agenda.'
                ], 500);
            }

            return redirect()->route('admin.agenda.index')
                ->with('error', 'Terjadi kesalahan saat menghapus agenda.');
        }
=======
        // Clear cache agendas setelah delete
        Cache::forget('agendas_all');

        return redirect()->route('admin.agenda.index')
            ->with('success', 'Agenda berhasil dihapus!');
>>>>>>> 284e251ce60564e812888c40ae43c01b7d4a7614
    }

    // Toggle link active status
    public function toggleLinkActive(Agenda $agenda)
{
    try {
        // Toggle status link_active
        $newStatus = !$agenda->link_active;
        $agenda->update(['link_active' => $newStatus]);

<<<<<<< HEAD
        return redirect()->route('admin.agenda.index');
    }
=======
        // Clear cache agendas setelah toggle status
        Cache::forget('agendas_all');

        // Prepare response data
        $responseData = [
            'success' => true,
            'link_active' => $newStatus,
            'message' => $newStatus 
                ? 'Link agenda berhasil diaktifkan!' 
                : 'Link agenda berhasil dinonaktifkan!'
        ];

        // Jika link diaktifkan, sertakan URL QR Code
        if ($newStatus) {
            $responseData['qrcode_url'] = route('admin.agenda.qrcode', $agenda);
        }

        // Check if request is AJAX
        if (request()->ajax()) {
            return response()->json($responseData);
        }

        // Fallback untuk non-AJAX request
        return redirect()->route('admin.agenda.index')
            ->with('success', $responseData['message']);
>>>>>>> 284e251ce60564e812888c40ae43c01b7d4a7614

    } catch (\Exception $e) {
        // Log error untuk debugging
        Log::error('Error toggling agenda link status: ' . $e->getMessage(), [
            'agenda_id' => $agenda->id,
            'user_id' => Auth::id(),
            'error' => $e->getTraceAsString()
        ]);

        $errorMessage = 'Terjadi kesalahan saat mengubah status link agenda.';

        // Check if request is AJAX
        if (request()->ajax()) {
            return response()->json([
                'success' => false,
                'message' => $errorMessage
            ], 500);
        }

        // Fallback untuk non-AJAX request
        return redirect()->route('admin.agenda.index')
            ->with('error', $errorMessage);
    }
}

    /**
     * Menampilkan halaman QR code untuk agenda
     *
     * Menggenerate QR code untuk URL pendaftaran publik agenda
     * dan menampilkan halaman dengan QR code tersebut.
     *
     * @param Agenda $agenda Instance agenda yang akan ditampilkan QR codenya
     * @return \Illuminate\View\View View halaman QR code
     */
    public function showQrCode(Agenda $agenda)
    {
        // Memuat relasi master dinas dan koordinator untuk agenda
        $agenda->load(['masterDinas', 'koordinator']);

        // Pastikan agenda memiliki token unik, jika belum buat token baru
        if (empty($agenda->unique_token)) {
            $agenda->unique_token = Agenda::generateUniqueToken();
            $agenda->save();
        }

        // Generate QR code untuk URL pendaftaran publik dengan token unik
        $publicUrl = route('agenda.public.register.token', ['token' => $agenda->unique_token]);
        $qrCode = QrCode::size(200)->generate($publicUrl);

        // Kembalikan view dengan data agenda, QR code, dan URL publik
        return view('admin.agenda.qrcode', compact('agenda', 'qrCode', 'publicUrl'));
    }

    /**
     * Mengekspor QR code agenda ke file PDF
     *
     * Menggenerate QR code, menyimpan link registrasi ke database,
     * dan mengekspor ke file PDF untuk didownload.
     *
     * @param Agenda $agenda Instance agenda yang akan diekspor QR codenya
     * @return \Illuminate\Http\Response File PDF download response
     */
    public function exportQrCodePdf(Agenda $agenda)
    {
        // Memuat relasi master dinas dan koordinator untuk agenda
        $agenda->load(['masterDinas', 'koordinator']);

        // Pastikan agenda memiliki token unik, jika belum buat token baru
        if (empty($agenda->unique_token)) {
            $agenda->unique_token = Agenda::generateUniqueToken();
            $agenda->save();
        }

        // Generate QR Code untuk URL pendaftaran publik dengan token unik
        $publicUrl = route('agenda.public.register.token', ['token' => $agenda->unique_token]);
        $qrCodeSvg = QrCode::size(200)->generate($publicUrl);

        // Konversi SVG QR Code ke base64 untuk embed di PDF
        $qrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg);

        // Simpan link registrasi ke database hanya jika link_acara kosong
        if (empty($agenda->link_acara)) {
            $agenda->update(['link_acara' => $publicUrl]);
        }

        // Generate PDF dengan view khusus yang menampilkan QR Code
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.agenda.export-pdf', [
            'agenda' => $agenda,
            'qrCodeBase64' => $qrCodeBase64,
            'publicUrl' => $publicUrl
        ]);

        // Download file PDF dengan nama khusus
        return $pdf->download('qrcode-agenda-' . $agenda->id . '.pdf');
    }
<<<<<<< HEAD

    /**
     * AJAX Search method untuk dynamic loading dropdown
     * Endpoint: /admin/agendas/search
     */
    public function search(Request $request)
    {
        try {
            $query = $request->get('q', '');
            $limit = (int) $request->get('limit', 50); // Batasi hasil untuk performa
            $showInactive = $request->boolean('show_inactive', false); // Parameter untuk menampilkan agenda non-aktif
            
            // Validasi input
            if ($limit > 100) {
                $limit = 100; // Maksimal 100 hasil untuk mencegah overload
            }
            
            // Cache key untuk hasil pencarian
            $cacheKey = 'agenda_search_' . md5($query . '_' . $limit . '_' . ($showInactive ? '1' : '0'));
            
            // Check cache terlebih dahulu (cache selama 5 menit)
            $cachedResult = Cache::remember($cacheKey, 300, function () use ($query, $limit, $showInactive) {
                // Query untuk mengambil data agenda dengan relasi master dinas
                $agendasQuery = Agenda::select([
                        'id', 
                        'nama_agenda', 
                        'tanggal_agenda as tanggal_mulai', // Alias untuk konsistensi dengan frontend
                        'link_active',
                        'dinas_id',
                        'created_at'
                    ])
                    ->with(['masterDinas:dinas_id,nama_dinas'])
                    ->orderBy('nama_agenda', 'asc');
                
                // Jika ada query pencarian, filter berdasarkan nama agenda
                if (!empty($query)) {
                    $agendasQuery->where('nama_agenda', 'LIKE', '%' . $query . '%');
                }
                
                // Filter hanya agenda aktif kecuali diminta untuk menampilkan yang non-aktif
                if (!$showInactive) {
                    $agendasQuery->where('link_active', true);
                }
                
                // Filter hanya agenda yang tanggalnya belum lewat (opsional)
                // Uncomment baris berikut jika ingin hanya menampilkan agenda mendatang
                // $agendasQuery->where('tanggal_agenda', '>=', now()->format('Y-m-d'));
                
                return $agendasQuery->limit($limit)->get();
            });
            
            // Format data untuk frontend
            $formattedAgendas = $cachedResult->map(function ($agenda) {
                return [
                    'id' => $agenda->id,
                    'nama_agenda' => $agenda->nama_agenda,
                    'tanggal_mulai' => $agenda->tanggal_mulai,
                    'status' => $agenda->link_active ? 'active' : 'inactive',
                    'dinas_nama' => $agenda->masterDinas->nama_dinas ?? 'N/A',
                    'created_at' => $agenda->created_at->format('Y-m-d H:i:s'),
                    // Tambahan informasi untuk UI yang lebih kaya
                    'display_text' => $agenda->nama_agenda,
                    'subtitle' => ($agenda->masterDinas->nama_dinas ?? 'N/A') . 
                                  ' • ' . 
                                  ($agenda->tanggal_mulai ? \Carbon\Carbon::parse($agenda->tanggal_mulai)->format('d M Y') : 'Tanggal belum ditentukan'),
                ];
            });
            
            // Log untuk debugging (hanya di development)
            if (config('app.debug')) {
                Log::info('Agenda search performed', [
                    'query' => $query,
                    'results_count' => $formattedAgendas->count(),
                    'limit' => $limit,
                    'show_inactive' => $showInactive
                ]);
            }
            
            return response()->json([
                'success' => true,
                'agendas' => $formattedAgendas,
                'total' => $formattedAgendas->count(),
                'query' => $query,
                'cached' => Cache::has($cacheKey),
                'timestamp' => now()->toISOString()
            ]);
            
        } catch (\Illuminate\Database\QueryException $e) {
            // Error database
            Log::error('Database error in agenda search', [
                'error' => $e->getMessage(),
                'query' => $request->get('q', ''),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada database saat mencari agenda',
                'agendas' => [],
                'total' => 0,
                'error_type' => 'database_error'
            ], 500);
            
        } catch (\Exception $e) {
            // Error umum lainnya
            Log::error('General error in agenda search', [
                'error' => $e->getMessage(),
                'query' => $request->get('q', ''),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem saat mencari agenda',
                'agendas' => [],
                'total' => 0,
                'error_type' => 'system_error'
            ], 500);
        }
    }

    /**
     * AJAX Load method untuk dynamic loading agenda list
     * Endpoint: /admin/agendas/load
     */
    public function load(Request $request)
    {
        try {
            // Get pagination parameters
            $page = (int) $request->get('page', 1);
            $perPage = (int) $request->get('per_page', 15);

            // Validate per_page to prevent excessive data loading
            if ($perPage > 100) {
                $perPage = 100;
            }

            // Build query with relationships
            $query = Agenda::with(['masterDinas', 'koordinator', 'agendaDetail'])
                ->latest();

            // Apply date filters if provided
            $query = $this->applyDateFilters($query, $request, 'tanggal_agenda');

            // Apply agenda_id filter if provided (for dropdown selection)
            if ($request->has('agenda_id') && !empty($request->agenda_id)) {
                $query->where('id', $request->agenda_id);
            }

            // Apply search filter if provided
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nama_agenda', 'LIKE', '%' . $search . '%')
                      ->orWhere('nama_koordinator', 'LIKE', '%' . $search . '%')
                      ->orWhereHas('masterDinas', function($dinasQuery) use ($search) {
                          $dinasQuery->where('nama_dinas', 'LIKE', '%' . $search . '%');
                      });
                });
            }

            // Get paginated results
            $agendas = $query->paginate($perPage, ['*'], 'page', $page);

            // Format data for frontend
            $formattedAgendas = $agendas->through(function ($agenda) {
                return [
                    'id' => $agenda->id,
                    'nama_agenda' => $agenda->nama_agenda,
                    'tanggal_agenda' => $agenda->tanggal_agenda,
                    'nama_koordinator' => $agenda->nama_koordinator,
                    'link_active' => $agenda->link_active,
                    'link_acara' => $agenda->link_acara,
                    'dinas_nama' => $agenda->masterDinas->nama_dinas ?? 'N/A',
                    'koordinator_name' => $agenda->koordinator->name ?? $agenda->nama_koordinator,
                    'participant_count' => $agenda->agendaDetail->count(),
                    'created_at' => $agenda->created_at->format('d/m/Y H:i'),
                    'formatted_date' => \Carbon\Carbon::parse($agenda->tanggal_agenda)->format('d/m/Y'),
                    'status_badge' => $agenda->link_active ?
                        '<span class="badge badge-success">Aktif</span>' :
                        '<span class="badge badge-secondary">Non-aktif</span>',
                    'actions' => view('admin.agenda.partials.actions', compact('agenda'))->render()
                ];
            });

            return response()->json([
                'success' => true,
                'agendas' => $formattedAgendas->values(),
                'pagination' => [
                    'current_page' => $agendas->currentPage(),
                    'last_page' => $agendas->lastPage(),
                    'per_page' => $agendas->perPage(),
                    'total' => $agendas->total(),
                    'from' => $agendas->firstItem(),
                    'to' => $agendas->lastItem(),
                    'has_more_pages' => $agendas->hasMorePages(),
                    'has_pages' => $agendas->hasPages()
                ],
                'filters' => [
                    'search' => $request->get('search', ''),
                    'tanggal' => $request->get('tanggal'),
                    'bulan' => $request->get('bulan'),
                    'tahun' => $request->get('tahun')
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading agendas: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memuat data agenda',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Method untuk clear cache agenda search
     * Berguna ketika ada perubahan data agenda
     */
    public function clearSearchCache()
    {
        try {
            // Clear semua cache yang berkaitan dengan pencarian agenda
            $cacheKeys = Cache::get('agenda_search_keys', []);
            
            foreach ($cacheKeys as $key) {
                Cache::forget($key);
            }
            
            // Clear cache popular agendas
            for ($i = 1; $i <= 50; $i++) {
                Cache::forget('popular_agendas_' . $i);
            }
            
            Cache::forget('agenda_search_keys');
            
            return response()->json([
                'success' => true,
                'message' => 'Cache berhasil dibersihkan'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error clearing agenda search cache: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal membersihkan cache'
            ], 500);
        }
    }
}
=======
}
    
>>>>>>> 284e251ce60564e812888c40ae43c01b7d4a7614
