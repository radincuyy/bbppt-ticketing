<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Prioritas;
use App\Http\Requests\StorePrioritasRequest;
use App\Http\Requests\UpdatePrioritasRequest;

class PrioritasController extends Controller
{
    /**
     * Menampilkan daftar prioritas.
     */
    public function index()
    {
        $prioritass = Prioritas::all();
        return view('admin.prioritas.index', compact('prioritass'));
    }

    /**
     * Menyimpan prioritas baru.
     */
    public function store(StorePrioritasRequest $request)
    {
        Prioritas::create($request->validated());

        return redirect()->route('admin.prioritas.index')
            ->with('success', 'Prioritas berhasil ditambahkan.');
    }

    /**
     * Memperbarui prioritas.
     */
    public function update(UpdatePrioritasRequest $request, $id)
    {
        $prioritas = Prioritas::findOrFail($id);
        
        $prioritas->update($request->validated());

        return redirect()->route('admin.prioritas.index')
            ->with('success', 'Prioritas berhasil diperbarui.');
    }

    /**
     * Menghapus prioritas.
     */
    public function destroy($id)
    {
        $prioritas = Prioritas::findOrFail($id);
        $prioritas->delete();

        return redirect()->route('admin.prioritas.index')
            ->with('success', 'Prioritas berhasil dihapus.');
    }
}
