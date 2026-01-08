<?php

namespace App\Http\Controllers;

use App\Models\Tiket;
use App\Models\User;
use App\Models\Kategori;
use App\Models\Status;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard berdasarkan role user.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Statistik umum
        $stats = $this->getStatistics($user);
        
        // Tiket terbaru berdasarkan role
        $recentTickets = $this->getRecentTickets($user);
        
        // Tiket yang perlu perhatian khusus
        $attentionTickets = $this->getAttentionTickets($user);
        
        // Data kinerja teknisi
        $kinerjaTeknisi = collect();
        if ($user->hasAnyRole(['TeamLead', 'ManagerTI'])) {
            $kinerjaTeknisi = $this->getKinerjaTeknisi();
        }
        
        return view('dashboard', compact('stats', 'recentTickets', 'attentionTickets', 'kinerjaTeknisi'));
    }

    /**
     * Mendapatkan statistik berdasarkan role user.
     */
    private function getStatistics(User $user): array
    {
        $stats = [];

        if ($user->hasAnyRole(['Helpdesk', 'TeamLead', 'ManagerTI'])) {
            // Staff melihat semua tiket
            $stats['total'] = Tiket::count();
            $stats['open'] = Tiket::whereHas('status', fn($q) => $q->where('nama_status', '!=', 'Closed'))->count();
            $stats['unassigned'] = Tiket::whereNull('id_teknisi')
                ->whereHas('status', fn($q) => $q->where('nama_status', '!=', 'Closed'))
                ->count();
            
            // Hitung tiket menunggu persetujuan
            $stats['pending_approval'] = Tiket::whereHas('status', fn($q) => $q->where('nama_status', 'Menunggu Persetujuan'))->count();
            
            // Breakdown status
            $stats['by_status'] = Status::withCount('tiket')->get();
            
            // Breakdown kategori
            $stats['by_category'] = Kategori::withCount('tiket')->get();
            
            // Tiket Selesai (Selesai + Closed)
            $stats['completed'] = Tiket::whereHas('status', fn($q) => $q->where('nama_status', 'Selesai'))->count();
            $stats['closed'] = Tiket::whereHas('status', fn($q) => $q->where('nama_status', 'Closed'))->count();
            
        } elseif ($user->hasRole('Technician')) {
            // Teknisi melihat tiket yang ditugaskan
            $stats['total'] = Tiket::byTeknisi($user->id)->count();
            $stats['open'] = Tiket::byTeknisi($user->id)
                ->whereHas('status', fn($q) => $q->where('nama_status', '!=', 'Closed'))
                ->count();
            
        } else {
            // Pemohon melihat tiket mereka sendiri
            $stats['total'] = Tiket::byPengguna($user->id)->count();
            $stats['open'] = Tiket::byPengguna($user->id)
                ->whereHas('status', fn($q) => $q->whereNotIn('nama_status', ['Closed', 'Selesai']))
                ->count();
            $stats['completed'] = Tiket::byPengguna($user->id)
                ->whereHas('status', fn($q) => $q->whereIn('nama_status', ['Closed', 'Selesai']))
                ->count();
        }

        // Persetujuan tertunda untuk Manager
        if ($user->hasRole('ManagerTI')) {
            $stats['pending_approvals'] = Tiket::whereHas('status', fn($q) => $q->where('nama_status', 'Menunggu Persetujuan'))->count();
        }

        return $stats;
    }

    /**
     * Mendapatkan tiket terbaru berdasarkan role user.
     */
    private function getRecentTickets(User $user)
    {
        $query = Tiket::with(['kategori', 'prioritas', 'status', 'pengguna', 'teknisi']);

        if ($user->hasAnyRole(['Helpdesk', 'TeamLead', 'ManagerTI'])) {
            return $query->orderBy('tanggal_dibuat', 'desc')->take(10)->get();
        } elseif ($user->hasRole('Technician')) {
            return $query->byTeknisi($user->id)->orderBy('tanggal_dibuat', 'desc')->take(10)->get();
        } else {
            return $query->byPengguna($user->id)->orderBy('tanggal_dibuat', 'desc')->take(10)->get();
        }
    }

    /**
     * Mendapatkan tiket yang butuh perhatian (Prioritas Tinggi, Belum Ditugaskan, dll).
     */
    private function getAttentionTickets(User $user)
    {
        $query = Tiket::with(['kategori', 'prioritas', 'status', 'pengguna']);

        if ($user->hasAnyRole(['Helpdesk', 'TeamLead', 'ManagerTI'])) {
            // Tiket belum ditugaskan yang masih terbuka
            return $query->unassigned()
                ->whereHas('status', fn($q) => $q->where('nama_status', '!=', 'Closed'))
                ->whereHas('prioritas', fn($q) => $q->where('nama_prioritas', 'Tinggi'))
                ->orderBy('tanggal_dibuat', 'desc')
                ->take(5)
                ->get();
        } elseif ($user->hasRole('Technician')) {
            return $query->byTeknisi($user->id)
                ->whereHas('status', fn($q) => $q->where('nama_status', '!=', 'Closed'))
                ->whereHas('prioritas', fn($q) => $q->where('nama_prioritas', 'Tinggi'))
                ->orderBy('tanggal_dibuat', 'desc')
                ->take(5)
                ->get();
        }

        return collect();
    }

    /**
     * Mendapatkan data kinerja staff Layanan TI
     */
    private function getKinerjaTeknisi()
    {
        $staffTI = User::role(['Helpdesk', 'Technician'])->get();
        
        return $staffTI->map(function ($staff) {
            $totalDitugaskan = Tiket::where('id_teknisi', $staff->id)->count();
            $sedangDikerjakan = Tiket::where('id_teknisi', $staff->id)
                ->whereHas('status', fn($q) => $q->whereNotIn('nama_status', ['Closed', 'Selesai']))
                ->count();
            $selesai = Tiket::where('id_teknisi', $staff->id)
                ->whereHas('status', fn($q) => $q->whereIn('nama_status', ['Closed', 'Selesai']))
                ->count();
            
            return [
                'nama' => $staff->name,
                'role' => $staff->getRoleNames()->first(),
                'total' => $totalDitugaskan,
                'dikerjakan' => $sedangDikerjakan,
                'selesai' => $selesai,
                'persentase' => $totalDitugaskan > 0 ? round(($selesai / $totalDitugaskan) * 100) : 0,
            ];
        });
    }
}
