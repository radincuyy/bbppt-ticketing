<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Category;
use App\Models\Priority;
use App\Models\Status;
use App\Models\User;
use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    /**
     * Display a listing of the tickets.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Ticket::with(['category', 'priority', 'status', 'requester', 'assignedTo']);

        // Role-based filtering - All tickets are visible to all roles
        if ($user->hasAnyRole(['Helpdesk', 'TeamLead', 'ManagerTI'])) {
            // Staff sees all tickets
        } elseif ($user->hasRole('Technician')) {
            $query->forTechnician($user->id);
        } else {
            // Requester sees their own tickets
            $query->byRequester($user->id);
        }

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status_id', $request->status);
        }
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        if ($request->filled('priority')) {
            $query->where('priority_id', $request->priority);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%");
            });
        }

        $tickets = $query->latest()->paginate(15);
        $statuses = Status::ordered()->get();
        $categories = Category::active()->get();
        $priorities = Priority::byUrgency()->get();

        return view('tickets.index', compact('tickets', 'statuses', 'categories', 'priorities'));
    }

    /**
     * Show the form for creating a new ticket.
     */
    public function create()
    {
        $categories = Category::active()->get();
        $priorities = Priority::byUrgency()->get();

        return view('tickets.create', compact('categories', 'priorities'));
    }

    /**
     * Store a newly created ticket.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'priority_id' => 'required|exists:priorities,id',
            'attachments.*' => 'nullable|file|max:10240', // 10MB max
        ]);

        // Get default status
        $defaultStatus = Status::getDefault() ?? Status::first();

        // Create ticket - no auto approval needed
        $ticket = Ticket::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category_id' => $validated['category_id'],
            'priority_id' => $validated['priority_id'],
            'status_id' => $defaultStatus->id,
            'requester_id' => Auth::id(),
            'needs_approval' => false,
            'approval_status' => null,
        ]);

        // Handle file attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments/tickets', 'public');
                
                Attachment::create([
                    'attachable_type' => Ticket::class,
                    'attachable_id' => $ticket->id,
                    'uploaded_by' => Auth::id(),
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Tiket berhasil dibuat dengan nomor: ' . $ticket->ticket_number);
    }

    /**
     * Display the specified ticket.
     */
    public function show(Ticket $ticket)
    {
        $user = Auth::user();

        // Authorization check
        if (!$this->canViewTicket($user, $ticket)) {
            abort(403);
        }

        $ticket->load(['category', 'priority', 'status', 'requester', 'assignedTo', 'approvedBy', 'comments.user', 'attachments.uploader']);

        // Get comments based on role (internal notes visibility)
        $comments = $ticket->comments;
        if (!$user->isStaff()) {
            $comments = $comments->where('is_internal', false);
        }

        $statuses = Status::ordered()->get();
        $categories = Category::where('is_active', true)->get();
        $priorities = Priority::orderBy('level')->get();
        
        // Tugaskan ke staff, jika helpdesk bisa menyelesaikan maka tidak perlu di tugas ke technician
        // Jika staff helpdesk tidak bisa menyelesaikan maka akan di tugas ke technician
        $helpdeskStaff = User::role('Helpdesk')->where('is_active', true)->get();
        $technicians = User::role('Technician')->where('is_active', true)->get();

        return view('tickets.show', compact('ticket', 'comments', 'statuses', 'categories', 'priorities', 'helpdeskStaff', 'technicians'));
    }

    /**
     * Update the specified ticket.
     */
    public function update(Request $request, Ticket $ticket)
    {
        $user = Auth::user();

        // Authorization check
        if (!$this->canUpdateTicket($user, $ticket)) {
            abort(403);
        }

        $validated = $request->validate([
            'status_id' => 'sometimes|exists:statuses,id',
            'category_id' => 'sometimes|exists:categories,id',
            'priority_id' => 'sometimes|exists:priorities,id',
            'assigned_to_id' => 'sometimes|nullable|exists:users,id',
            'resolution_notes' => 'sometimes|nullable|string',
        ]);

        // Track status change
        $oldStatusId = $ticket->status_id;

        $ticket->update($validated);

        // If status changed to resolved or closed, set resolved_at
        if (isset($validated['status_id']) && $validated['status_id'] != $oldStatusId) {
            $newStatus = Status::find($validated['status_id']);
            if (in_array($newStatus->slug, ['resolved', 'closed']) && !$ticket->resolved_at) {
                $ticket->update(['resolved_at' => now()]);
            }
        }

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Tiket berhasil diupdate.');
    }

    /**
     * Assign ticket to technician (individual user)
     */
    public function assign(Request $request, Ticket $ticket)
    {
        $user = Auth::user();

        if (!$user->can('tickets.assign')) {
            abort(403);
        }

        $validated = $request->validate([
            'assigned_to_id' => 'required|exists:users,id',
        ]);

        $technician = User::find($validated['assigned_to_id']);
        $ticket->update(['assigned_to_id' => $technician->id]);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Tiket berhasil ditugaskan ke ' . $technician->name);
    }

    /**
     * Request approval from Manager (UC-5)
     */
    public function requestApproval(Request $request, Ticket $ticket)
    {
        $user = Auth::user();

        // Only Helpdesk and Technician can request approval
        if (!$user->hasAnyRole(['Helpdesk', 'Technician'])) {
            abort(403);
        }

        // Get pending approval status
        $pendingApprovalStatus = Status::where('slug', 'pending-approval')->first();
        
        if (!$pendingApprovalStatus) {
            return back()->with('error', 'Status "Menunggu Persetujuan" tidak ditemukan.');
        }

        $ticket->update([
            'status_id' => $pendingApprovalStatus->id,
            'needs_approval' => true,
            'approval_status' => 'pending',
        ]);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Tiket berhasil diajukan untuk persetujuan Manager.');
    }

    /**
     * Close ticket (only if status is Resolved - UC-3)
     */
    public function close(Request $request, Ticket $ticket)
    {
        $user = Auth::user();

        // Check permission
        $canClose = $user->can('tickets.close.all') || 
                    ($user->can('tickets.close.own') && $ticket->requester_id === $user->id);

        if (!$canClose) {
            abort(403);
        }

        // Pemohon hanya bisa close jika status "Resolved"
        if ($user->id === $ticket->requester_id && !$user->isStaff()) {
            $resolvedStatus = Status::where('slug', 'resolved')->first();
            if ($ticket->status_id !== $resolvedStatus?->id) {
                return back()->with('error', 'Tiket hanya bisa ditutup jika sudah berstatus "Resolved".');
            }
        }

        $closedStatus = Status::where('slug', 'closed')->first();
        $ticket->updateStatus($closedStatus);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Tiket berhasil ditutup.');
    }

    /**
     * Check if user can view ticket
     */
    private function canViewTicket(User $user, Ticket $ticket): bool
    {
        if ($user->can('tickets.view.all')) return true;
        if ($user->can('tickets.view.assigned') && $ticket->assigned_to_id === $user->id) return true;
        if ($user->can('tickets.view.own') && $ticket->requester_id === $user->id) return true;
        return false;
    }

    /**
     * Check if user can update ticket
     */
    private function canUpdateTicket(User $user, Ticket $ticket): bool
    {
        if ($user->can('tickets.update.all')) return true;
        if ($user->can('tickets.update.assigned') && $ticket->assigned_to_id === $user->id) return true;
        if ($user->can('tickets.update.own') && $ticket->requester_id === $user->id) return true;
        return false;
    }
}
