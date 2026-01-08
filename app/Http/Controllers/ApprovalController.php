<?php

namespace App\Http\Controllers;

use App\Models\Tiket;
use App\Services\TiketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApprovalController extends Controller
{
    protected $tiketService;

    public function __construct(TiketService $tiketService)
    {
        $this->tiketService = $tiketService;
    }

    /**
     * Menampilkan daftar tiket yang menunggu persetujuan.
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user->can('approvals.view')) {
            abort(403);
        }

        // Ambil tiket dengan status "Menunggu Persetujuan"
        // Query ini spesifik read-only untuk view, jadi tidak harus masuk service, 
        // tapi logikanya tetap dijaga bersih.
        $pendingTickets = Tiket::with(['kategori', 'prioritas', 'status', 'pengguna'])
            ->whereHas('status', fn($q) => $q->where('nama_status', 'Menunggu Persetujuan'))
            ->orderBy('tanggal_dibuat', 'desc')
            ->paginate(15);

        return view('approvals.index', compact('pendingTickets'));
    }

    /**
     * Menyetujui tiket.
     */
    public function approve(Request $request, $id)
    {
        $user = Auth::user();
        $tiket = Tiket::findOrFail($id);

        if (!$user->can('approvals.approve')) {
            abort(403);
        }

        $this->tiketService->approveTiket($tiket);

        return redirect()->route('approvals.index')
            ->with('success', 'Tiket ' . $tiket->nomor_tiket . ' berhasil disetujui.');
    }

    /**
     * Menolak tiket.
     */
    public function reject(Request $request, $id)
    {
        $user = Auth::user();
        $tiket = Tiket::findOrFail($id);

        if (!$user->can('approvals.reject')) {
            abort(403);
        }

        $validated = $request->validate([
            'alasan' => 'required|string|max:500',
        ]);

        $this->tiketService->rejectTiket($tiket, $validated['alasan']);

        return redirect()->route('approvals.index')
            ->with('success', 'Tiket ' . $tiket->nomor_tiket . ' ditolak.');
    }
}
