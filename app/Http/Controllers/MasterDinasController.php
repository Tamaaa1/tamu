<?php

namespace App\Http\Controllers;

use App\Models\MasterDinas;
use Illuminate\Http\Request;

class MasterDinasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $dinas = MasterDinas::all();
        return view('admin.master-dinas.index', compact('dinas'));
    }

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

        // Generate unique dinas_id
        $validated['dinas_id'] = 'DIN-' . strtoupper(substr($validated['nama_dinas'], 0, 3)) . '-' . time();

        MasterDinas::create($validated);

        return redirect()->route('admin.master-dinas.index')
            ->with('success', 'Dinas berhasil ditambahkan!');
    }

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

        $masterDina->update($validated);

        return redirect()->route('admin.master-dinas.index')
            ->with('success', 'Dinas berhasil diupdate!');
    }

    public function destroy(MasterDinas $masterDina)
    {
        $masterDina->delete();
        return redirect()->route('admin.master-dinas.index')
            ->with('success', 'Dinas berhasil dihapus!');
    }
}
