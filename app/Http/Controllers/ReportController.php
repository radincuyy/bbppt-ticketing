<?php

namespace App\Http\Controllers;

use App\Models\Tiket;
use App\Models\Kategori;
use App\Models\Status;
use App\Models\Prioritas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Menampilkan halaman laporan dengan filter.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Hanya Manager dan TeamLead yang bisa akses laporan
        if (!$user->hasAnyRole(['ManagerTI', 'TeamLead'])) {
            abort(403);
        }

        // Ambil parameter filter
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $kategoriId = $request->get('id_kategori');
        $statusId = $request->get('id_status');
        $prioritasId = $request->get('id_prioritas');

        // Buat query
        $query = Tiket::with(['kategori', 'prioritas', 'status', 'pengguna', 'teknisi'])
            ->whereBetween('tanggal_dibuat', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        if ($kategoriId) {
            $query->where('id_kategori', $kategoriId);
        }
        if ($statusId) {
            $query->where('id_status', $statusId);
        }
        if ($prioritasId) {
            $query->where('id_prioritas', $prioritasId);
        }

        $tikets = $query->orderBy('tanggal_dibuat', 'desc')->get();

        // Statistik Laporan
        $stats = [
            'total' => $tikets->count(),
            'open' => $tikets->filter(fn($t) => $t->status->nama_status !== 'Closed')->count(),
            'closed' => $tikets->filter(fn($t) => $t->status->nama_status === 'Closed')->count(),
            'by_category' => $tikets->groupBy('kategori.nama_kategori')->map->count(),
            'by_priority' => $tikets->groupBy('prioritas.nama_prioritas')->map->count(),
            'by_status' => $tikets->groupBy('status.nama_status')->map->count(),
        ];

        // Opsi filter
        $kategoris = Kategori::all();
        $statuses = Status::all();
        $prioritass = Prioritas::all();

        return view('reports.index', compact(
            'tikets', 
            'stats', 
            'kategoris', 
            'statuses', 
            'prioritass',
            'startDate',
            'endDate',
            'kategoriId',
            'statusId',
            'prioritasId'
        ));
    }

    /**
     * Ekspor Laporan ke Excel (CSV).
     */
    public function exportExcel(Request $request)
    {
        $user = Auth::user();

        if (!$user->hasAnyRole(['ManagerTI', 'TeamLead'])) {
            abort(403);
        }

        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        $query = Tiket::with(['kategori', 'prioritas', 'status', 'pengguna', 'teknisi'])
            ->whereBetween('tanggal_dibuat', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        if ($request->get('id_kategori')) {
            $query->where('id_kategori', $request->get('id_kategori'));
        }
        if ($request->get('id_status')) {
            $query->where('id_status', $request->get('id_status'));
        }
        if ($request->get('id_prioritas')) {
            $query->where('id_prioritas', $request->get('id_prioritas'));
        }

        $tikets = $query->orderBy('tanggal_dibuat', 'desc')->get();

        $filename = 'laporan_tiket_' . $startDate . '_' . $endDate . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($tikets) {
            $file = fopen('php://output', 'w');
            
            // Tambahkan BOM untuk kompatibilitas Excel UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Baris Header
            fputcsv($file, [
                'No. Tiket',
                'Judul',
                'Kategori',
                'Prioritas',
                'Status',
                'Pemohon',
                'Ditugaskan Ke',
                'Tanggal Dibuat',
            ]);

            // Baris Data
            foreach ($tikets as $tiket) {
                fputcsv($file, [
                    $tiket->nomor_tiket,
                    $tiket->judul,
                    $tiket->kategori->nama_kategori ?? '-',
                    $tiket->prioritas->nama_prioritas ?? '-',
                    $tiket->status->nama_status ?? '-',
                    $tiket->pengguna->name ?? '-',
                    $tiket->teknisi->name ?? 'Belum ditugaskan',
                    $tiket->tanggal_dibuat->format('d/m/Y H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Ekspor Laporan ke PDF.
     */
    public function exportPdf(Request $request)
    {
        $user = Auth::user();

        if (!$user->hasAnyRole(['ManagerTI', 'TeamLead'])) {
            abort(403);
        }

        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        $query = Tiket::with(['kategori', 'prioritas', 'status', 'pengguna', 'teknisi'])
            ->whereBetween('tanggal_dibuat', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        if ($request->get('id_kategori')) {
            $query->where('id_kategori', $request->get('id_kategori'));
        }
        if ($request->get('id_status')) {
            $query->where('id_status', $request->get('id_status'));
        }
        if ($request->get('id_prioritas')) {
            $query->where('id_prioritas', $request->get('id_prioritas'));
        }

        $tikets = $query->orderBy('tanggal_dibuat', 'desc')->get();

        // Statistik
        $stats = [
            'total' => $tikets->count(),
            'open' => $tikets->filter(fn($t) => $t->status->nama_status !== 'Closed')->count(),
            'closed' => $tikets->filter(fn($t) => $t->status->nama_status === 'Closed')->count(),
            'by_category' => $tikets->groupBy('kategori.nama_kategori')->map->count(),
            'by_status' => $tikets->groupBy('status.nama_status')->map->count(),
        ];

        return view('reports.pdf', compact('tikets', 'stats', 'startDate', 'endDate'));
    }
}
