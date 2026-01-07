<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApprovalController extends Controller
{
    /**
     * Show pending approvals
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user->can('approvals.view')) {
            abort(403);
        }

        // Get tickets with status "Menunggu Persetujuan"
        $pendingTickets = Ticket::with(['category', 'priority', 'status', 'requester'])
            ->whereHas('status', fn($q) => $q->where('slug', 'pending-approval'))
            ->latest()
            ->paginate(15);
        
        // Count approved and rejected this month
        $approvedCount = Ticket::where('approval_status', 'approved')
            ->whereMonth('approved_at', now()->month)
            ->count();
        $rejectedCount = Ticket::where('approval_status', 'rejected')
            ->whereMonth('approved_at', now()->month)
            ->count();

        return view('approvals.index', compact('pendingTickets', 'approvedCount', 'rejectedCount'));
    }

    /**
     * Approve a ticket
     */
    public function approve(Request $request, Ticket $ticket)
    {
        $user = Auth::user();

        if (!$user->can('approvals.approve')) {
            abort(403);
        }

        $validated = $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $ticket->approve($user, $validated['notes'] ?? null);

        return redirect()->route('approvals.index')
            ->with('success', 'Tiket ' . $ticket->ticket_number . ' berhasil disetujui.');
    }

    /**
     * Reject a ticket
     */
    public function reject(Request $request, Ticket $ticket)
    {
        $user = Auth::user();

        if (!$user->can('approvals.reject')) {
            abort(403);
        }

        $validated = $request->validate([
            'notes' => 'required|string|max:500',
        ]);

        $ticket->reject($user, $validated['notes']);

        return redirect()->route('approvals.index')
            ->with('success', 'Tiket ' . $ticket->ticket_number . ' ditolak.');
    }
}
