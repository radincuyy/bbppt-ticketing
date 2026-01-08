<?php

namespace App\Http\Controllers;

use App\Models\Tiket;
use App\Services\TiketService;
use Illuminate\Http\Request;

class KomentarController extends Controller
{
    /**
     * Menyimpan komentar baru pada tiket.
     */
    public function store(Request $request, TiketService $service, $id)
    {
        $tiket = Tiket::findOrFail($id);

        // Validasi input
        $validated = $request->validate([
            'isi_komentar' => 'required|string',
        ]);

        // Gunakan service
        $service->addKomentar($tiket, $validated['isi_komentar']);

        return redirect()->route('tickets.show', $tiket->id_tiket)
            ->with('success', 'Komentar berhasil ditambahkan.');
    }
}
