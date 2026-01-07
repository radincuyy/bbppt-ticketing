<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use App\Models\Category;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display dashboard based on user role
     */
    public function index()
    {
        $user = Auth::user();
        
        // Common statistics
        $stats = $this->getStatistics($user);
        
        // Recent tickets based on role
        $recentTickets = $this->getRecentTickets($user);
        
        // Get tickets needing attention
        $attentionTickets = $this->getAttentionTickets($user);
        
        return view('dashboard', compact('stats', 'recentTickets', 'attentionTickets'));
    }

    /**
     * Get statistics based on user role
     */
    private function getStatistics(User $user): array
    {
        $stats = [];

        if ($user->hasAnyRole(['Helpdesk', 'TeamLead', 'ManagerTI'])) {
            // Staff sees all tickets
            $stats['total'] = Ticket::count();
            $stats['open'] = Ticket::open()->count();
            $stats['unassigned'] = Ticket::unassigned()->open()->count();
            $stats['resolved_today'] = Ticket::whereDate('resolved_at', today())->count();
            
            // Count pending approval tickets
            $stats['pending_approval'] = Ticket::whereHas('status', fn($q) => $q->where('slug', 'pending-approval'))->count();
            
            // By status breakdown
            $stats['by_status'] = Status::withCount('tickets')->ordered()->get();
            
            // By category breakdown
            $stats['by_category'] = Category::withCount('tickets')->get();
            
        } elseif ($user->hasRole('Technician')) {
            // Technician sees assigned tickets
            $stats['total'] = Ticket::forTechnician($user->id)->count();
            $stats['open'] = Ticket::forTechnician($user->id)->open()->count();
            $stats['resolved_today'] = Ticket::forTechnician($user->id)->whereDate('resolved_at', today())->count();
            
        } else {
            // Requester sees own tickets
            $stats['total'] = Ticket::byRequester($user->id)->count();
            $stats['open'] = Ticket::byRequester($user->id)->open()->count();
            $stats['in_progress'] = Ticket::byRequester($user->id)
                ->whereHas('status', fn($q) => $q->where('slug', 'in-progress'))
                ->count();
            $stats['closed'] = Ticket::byRequester($user->id)->closed()->count();
        }

        // Pending approvals for Manager
        if ($user->hasRole('ManagerTI')) {
            $stats['pending_approvals'] = Ticket::pendingApproval()->count();
        }

        return $stats;
    }

    /**
     * Get recent tickets based on user role
     */
    private function getRecentTickets(User $user)
    {
        $query = Ticket::with(['category', 'priority', 'status', 'requester', 'assignedTo']);

        if ($user->hasAnyRole(['Helpdesk', 'TeamLead', 'ManagerTI'])) {
            // Staff sees all tickets
            return $query->latest()->take(10)->get();
        } elseif ($user->hasRole('Technician')) {
            // Technician sees assigned tickets
            return $query->forTechnician($user->id)->latest()->take(10)->get();
        } else {
            // Requester sees own tickets
            return $query->byRequester($user->id)->latest()->take(10)->get();
        }
    }

    /**
     * Get tickets needing attention
     */
    private function getAttentionTickets(User $user)
    {
        $query = Ticket::with(['category', 'priority', 'status', 'requester']);

        if ($user->hasAnyRole(['Helpdesk', 'TeamLead', 'ManagerTI'])) {
            // Unassigned high priority tickets that are still open
            return $query->unassigned()
                ->open()
                ->whereHas('priority', fn($q) => $q->where('level', '>=', 3))
                ->latest()
                ->take(5)
                ->get();
        } elseif ($user->hasRole('Technician')) {
            // Assigned high priority tickets
            return $query->forTechnician($user->id)
                ->open()
                ->whereHas('priority', fn($q) => $q->where('level', '>=', 3))
                ->latest()
                ->take(5)
                ->get();
        }

        return collect();
    }
}
