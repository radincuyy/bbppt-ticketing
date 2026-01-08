<?php

namespace App\Http\Controllers;

use App\Models\Tiket;
use App\Models\Kategori;
use App\Models\Prioritas;
use App\Models\Status;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreTiketRequest;
use App\Http\Requests\UpdateTiketRequest;
use App\Services\TiketService;

class TiketController extends Controller
{
    /**
     * Menampilkan daftar tiket.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Tiket::with(['kategori', 'prioritas', 'status', 'pengguna', 'teknisi']);

        // Filter berdasarkan role
        if ($user->hasAnyRole(['Helpdesk', 'TeamLead', 'ManagerTI'])) {
            // Staff melihat semua tiket
        } elseif ($user->hasRole('Technician')) {
            // Teknisi melihat tiket yang ditugaskan dan tiket yang belum ditugaskan/terbuka
            $query->where(function($q) use ($user) {
                $q->byTeknisi($user->id);
            });
        } else {
            // Pemohon hanya melihat tiket mereka sendiri
            $query->byPengguna($user->id);
        }

        // Terapkan filter pencarian
        if ($request->filled('status')) {
            $query->where('id_status', $request->status);
        }
        if ($request->filled('kategori')) {
            $query->where('id_kategori', $request->kategori);
        }
        if ($request->filled('prioritas')) {
            $query->where('id_prioritas', $request->prioritas);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_tiket', 'like', "%{$search}%")
                  ->orWhere('judul', 'like', "%{$search}%");
            });
        }

        // Urutkan berdasarkan tanggal terbaru
        $tikets = $query->orderBy('tanggal_dibuat', 'desc')->paginate(15);
        
        // Data master untuk filter
        $statuses = Status::all();
        $kategoris = Kategori::all();
        $prioritass = Prioritas::all();

        return view('tickets.index', compact('tikets', 'statuses', 'kategoris', 'prioritass'));
    }

    /**
     * Menampilkan daftar tugas (tiket yang ditugaskan ke user).
     */
    public function tasks(Request $request)
    {
        $user = Auth::user();
        
        // Hanya Helpdesk dan Teknisi yang bisa akses
        if (!$user->hasAnyRole(['Helpdesk', 'Technician'])) {
            abort(403);
        }
        
        $query = Tiket::with(['kategori', 'prioritas', 'status', 'pengguna', 'teknisi'])
            ->where('id_teknisi', $user->id);

        // Terapkan filter pencarian
        if ($request->filled('status')) {
            $query->where('id_status', $request->status);
        }
        if ($request->filled('kategori')) {
            $query->where('id_kategori', $request->kategori);
        }
        if ($request->filled('prioritas')) {
            $query->where('id_prioritas', $request->prioritas);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_tiket', 'like', "%{$search}%")
                  ->orWhere('judul', 'like', "%{$search}%");
            });
        }

        $tikets = $query->orderBy('tanggal_dibuat', 'desc')->paginate(15);
        
        // Data master untuk filter
        $statuses = Status::all();
        $kategoris = Kategori::all();
        $prioritass = Prioritas::all();

        return view('tickets.tasks', compact('tikets', 'statuses', 'kategoris', 'prioritass'));
    }

    /**
     * Menampilkan form untuk membuat tiket baru.
     */
    public function create()
    {
        $kategoris = Kategori::all();
        $prioritass = Prioritas::all();

        return view('tickets.create', compact('kategoris', 'prioritass'));
    }

    /**
     * Menyimpan tiket baru ke database.
     */
    public function store(StoreTiketRequest $request, TiketService $service)
    {
        $tiket = $service->createTiket(
            $request->validated(), 
            $request->file('attachments')
        );

        return redirect()->route('tickets.show', $tiket->id_tiket)
            ->with('success', 'Tiket berhasil dibuat dengan nomor: ' . $tiket->nomor_tiket);
    }

    /**
     * Menampilkan detail tiket spesifik.
     */
    public function show($id)
    {
        $user = Auth::user();
        $tiket = Tiket::with(['kategori', 'prioritas', 'status', 'pengguna', 'teknisi', 'komentar.pengguna', 'lampiran'])->findOrFail($id);

        // Cek otorisasi akses
        if (!$this->canViewTiket($user, $tiket)) {
            abort(403);
        }

        $komentars = $tiket->komentar;
        $statuses = Status::all();
        $kategoris = Kategori::all();
        $prioritass = Prioritas::all();
        
        // Daftar staff untuk penugasan
        $helpdeskStaff = User::role('Helpdesk')->get();
        $technicians = User::role('Technician')->get();

        return view('tickets.show', compact('tiket', 'komentars', 'statuses', 'kategoris', 'prioritass', 'helpdeskStaff', 'technicians'));
    }

    /**
     * Memperbarui data tiket (Status, Kategori, Prioritas, Teknisi).
     */
    public function update(UpdateTiketRequest $request, TiketService $service, $id)
    {
        $user = Auth::user();
        $tiket = Tiket::findOrFail($id);

        // Cek otorisasi update
        if (!$this->canUpdateTiket($user, $tiket)) {
            abort(403);
        }

        $service->updateTiket($tiket, $request->validated());

        return redirect()->route('tickets.show', $tiket->id_tiket)
            ->with('success', 'Tiket berhasil diperbarui.');
    }

    /**
     * Menugaskan tiket ke teknisi.
     */
    public function assign(Request $request, TiketService $service, $id)
    {
        $user = Auth::user();
        $tiket = Tiket::findOrFail($id);

        if (!$user->can('tickets.assign')) {
            abort(403);
        }

        $validated = $request->validate([
            'id_teknisi' => 'required|exists:users,id',
        ]);

        $service->assignTeknisi($tiket, $validated['id_teknisi']);

        $teknisi = User::find($validated['id_teknisi']); // Fetch ulang untuk nama pesan sukses
        
        return redirect()->route('tickets.show', $tiket->id_tiket)
            ->with('success', 'Tiket berhasil ditugaskan ke ' . $teknisi->name);
    }

    /**
     * Mengajukan persetujuan tiket ke Manager.
     */
    public function requestApproval(Request $request, TiketService $service, $id)
    {
        $user = Auth::user();
        $tiket = Tiket::findOrFail($id);

        // Hanya Helpdesk dan Teknisi yang bisa mengajukan persetujuan
        if (!$user->hasAnyRole(['Helpdesk', 'Technician'])) {
            abort(403);
        }

        $service->requestApproval($tiket);

        return redirect()->route('tickets.show', $tiket->id_tiket)
            ->with('success', 'Tiket berhasil diajukan untuk persetujuan Manager.');
    }

    /**
     * Menutup tiket (Closed).
     */
    public function close(Request $request, TiketService $service, $id)
    {
        $user = Auth::user();
        $tiket = Tiket::findOrFail($id);

        // Cek hak akses penutupan
        $canClose = $user->can('tickets.close.all') || 
                    ($user->can('tickets.close.own') && $tiket->id_pengguna === $user->id);

        if (!$canClose) {
            abort(403);
        }

        // Pemohon hanya bisa close jika status sudah "Selesai"
        if ($user->id === $tiket->id_pengguna && !$user->isStaff()) {
            $selesaiStatus = Status::where('nama_status', 'Selesai')->first();
            if ($tiket->id_status !== $selesaiStatus?->id_status) {
                return back()->with('error', 'Tiket hanya bisa ditutup jika sudah berstatus "Selesai".');
            }
        }

        $service->closeTiket($tiket);

        return redirect()->route('tickets.show', $tiket->id_tiket)
            ->with('success', 'Tiket berhasil ditutup.');
    }

    /**
     * Cek apakah user boleh melihat tiket.
     */
    private function canViewTiket(User $user, Tiket $tiket): bool
    {
        if ($user->can('tickets.view.all')) return true;
        if ($user->can('tickets.view.assigned') && $tiket->id_teknisi === $user->id) return true;
        if ($user->can('tickets.view.own') && $tiket->id_pengguna === $user->id) return true;
        return false;
    }

    /**
     * Cek apakah user boleh mengubah tiket.
     */
    private function canUpdateTiket(User $user, Tiket $tiket): bool
    {
        if ($user->can('tickets.update.all')) return true;
        if ($user->can('tickets.update.assigned') && $tiket->id_teknisi === $user->id) return true;
        if ($user->can('tickets.update.own') && $tiket->id_pengguna === $user->id) return true;
        return false;
    }
}
