<?php

namespace App\Http\Controllers;

use App\Models\MasterDinas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MasterDinasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    // Menampilkan daftar nama dinas
    public function index()
    {
        $dinas = MasterDinas::orderBy('nama_dinas')->paginate(15);
        return view('admin.master-dinas.index', compact('dinas'));
    }
    // Menampilkan halaman untuk membuat dinas baru
    public function create()
    {
        return view('admin.master-dinas.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_dinas' => 'required|string|max:255',
            'alamat' => 'nullable|string|max:500',
            'email' => 'nullable|email|max:255'
        ]);

        try {
            // Generate unique dinas_id
            $validated['dinas_id'] = 'DIN-' . strtoupper(substr($validated['nama_dinas'], 0, 3)) . '-' . time();

            MasterDinas::create($validated);

            Log::info('Instansi baru dibuat: ' . $validated['nama_dinas']);

            return redirect()->route('admin.master-dinas.index')
                ->with('success', 'Instansi berhasil ditambahkan!');
        } catch (\Exception $e) {
            Log::error('Gagal membuat dinas: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat menyimpan data dinas.']);
        }
    }
    // Menampilkan halaman untuk mengedit dinas
    public function edit(MasterDinas $masterDina)
    {
        return view('admin.master-dinas.edit', compact('masterDina'));
    }

    public function update(Request $request, MasterDinas $masterDina)
    {
        $validated = $request->validate([
            'nama_dinas' => 'required|string|max:255',
            'alamat' => 'nullable|string|max:500',
            'email' => 'nullable|email|max:255'
        ]);

        try {
            $masterDina->update($validated);

            Log::info('Dinas diupdate: ' . $validated['nama_dinas']);

            return redirect()->route('admin.master-dinas.index')
                ->with('success', 'Instansi berhasil diupdate!');
        } catch (\Exception $e) {
            Log::error('Gagal update dinas: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat mengupdate data instansi.']);
        }
    }
    // Menghapus dinas
    public function destroy(MasterDinas $masterDina)
    {
        $masterDina->delete();
        return redirect()->route('admin.master-dinas.index')
            ->with('success', 'Instansi berhasil dihapus!');
    }


}
