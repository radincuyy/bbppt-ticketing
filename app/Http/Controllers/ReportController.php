<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Category;
use App\Models\Status;
use App\Models\Priority;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display report page with filters
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Only Manager and TeamLead can access reports
        if (!$user->hasAnyRole(['ManagerTI', 'TeamLead'])) {
            abort(403);
        }

        // Get filter parameters
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $categoryId = $request->get('category_id');
        $statusId = $request->get('status_id');
        $priorityId = $request->get('priority_id');

        // Build query
        $query = Ticket::with(['category', 'priority', 'status', 'requester', 'assignedTo'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }
        if ($statusId) {
            $query->where('status_id', $statusId);
        }
        if ($priorityId) {
            $query->where('priority_id', $priorityId);
        }

        $tickets = $query->orderBy('created_at', 'desc')->get();

        // Statistics
        $stats = [
            'total' => $tickets->count(),
            'open' => $tickets->where('status.is_closed', false)->count(),
            'closed' => $tickets->where('status.is_closed', true)->count(),
            'by_category' => $tickets->groupBy('category.name')->map->count(),
            'by_priority' => $tickets->groupBy('priority.name')->map->count(),
            'by_status' => $tickets->groupBy('status.name')->map->count(),
        ];

        // Get filter options
        $categories = Category::where('is_active', true)->get();
        $statuses = Status::ordered()->get();
        $priorities = Priority::orderBy('level')->get();

        return view('reports.index', compact(
            'tickets', 
            'stats', 
            'categories', 
            'statuses', 
            'priorities',
            'startDate',
            'endDate',
            'categoryId',
            'statusId',
            'priorityId'
        ));
    }

    /**
     * Export to Excel (CSV)
     */
    public function exportExcel(Request $request)
    {
        $user = Auth::user();

        if (!$user->hasAnyRole(['ManagerTI', 'TeamLead'])) {
            abort(403);
        }

        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        $query = Ticket::with(['category', 'priority', 'status', 'requester', 'assignedTo'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        if ($request->get('category_id')) {
            $query->where('category_id', $request->get('category_id'));
        }
        if ($request->get('status_id')) {
            $query->where('status_id', $request->get('status_id'));
        }
        if ($request->get('priority_id')) {
            $query->where('priority_id', $request->get('priority_id'));
        }

        $tickets = $query->orderBy('created_at', 'desc')->get();

        $filename = 'laporan_tiket_' . $startDate . '_' . $endDate . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($tickets) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for Excel UTF-8 compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header row
            fputcsv($file, [
                'No. Tiket',
                'Judul',
                'Kategori',
                'Prioritas',
                'Status',
                'Pemohon',
                'Ditugaskan Ke',
                'Tanggal Dibuat',
                'Tanggal Selesai',
            ]);

            // Data rows
            foreach ($tickets as $ticket) {
                fputcsv($file, [
                    $ticket->ticket_number,
                    $ticket->title,
                    $ticket->category->name ?? '-',
                    $ticket->priority->name ?? '-',
                    $ticket->status->name ?? '-',
                    $ticket->requester->name ?? '-',
                    $ticket->assignedTo->name ?? 'Belum ditugaskan',
                    $ticket->created_at->format('d/m/Y H:i'),
                    $ticket->resolved_at ? $ticket->resolved_at->format('d/m/Y H:i') : '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export to PDF
     */
    public function exportPdf(Request $request)
    {
        $user = Auth::user();

        if (!$user->hasAnyRole(['ManagerTI', 'TeamLead'])) {
            abort(403);
        }

        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        $query = Ticket::with(['category', 'priority', 'status', 'requester', 'assignedTo'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        if ($request->get('category_id')) {
            $query->where('category_id', $request->get('category_id'));
        }
        if ($request->get('status_id')) {
            $query->where('status_id', $request->get('status_id'));
        }
        if ($request->get('priority_id')) {
            $query->where('priority_id', $request->get('priority_id'));
        }

        $tickets = $query->orderBy('created_at', 'desc')->get();

        // Statistics
        $stats = [
            'total' => $tickets->count(),
            'open' => $tickets->where('status.is_closed', false)->count(),
            'closed' => $tickets->where('status.is_closed', true)->count(),
            'by_category' => $tickets->groupBy('category.name')->map->count(),
            'by_status' => $tickets->groupBy('status.name')->map->count(),
        ];

        return view('reports.pdf', compact('tickets', 'stats', 'startDate', 'endDate'));
    }
}
