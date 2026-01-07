<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Ticket extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'ticket_number',
        'title',
        'description',
        'category_id',
        'priority_id',
        'status_id',
        'requester_id',
        'assigned_to_id',
        'assigned_role',  // Role-based assignment
        'needs_approval',
        'approval_status',
        'approved_by_id',
        'approved_at',
        'approval_notes',
        'resolved_at',
        'closed_at',
        'resolution_notes',
    ];

    protected $casts = [
        'needs_approval' => 'boolean',
        'approved_at' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    /**
     * Activity log options
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'status_id', 'assigned_to_id', 'priority_id', 'approval_status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Boot function for auto-generating ticket number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
            if (empty($ticket->ticket_number)) {
                $ticket->ticket_number = static::generateTicketNumber();
            }
        });
    }

    /**
     * Generate unique ticket number
     */
    public static function generateTicketNumber(): string
    {
        $prefix = 'TKT';
        $date = now()->format('Ymd');
        $lastTicket = static::whereDate('created_at', now()->toDateString())
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastTicket ? intval(substr($lastTicket->ticket_number, -4)) + 1 : 1;
        
        return sprintf('%s-%s-%04d', $prefix, $date, $sequence);
    }

    // Relationships
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function priority(): BelongsTo
    {
        return $this->belongsTo(Priority::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->orderBy('created_at');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    // Scopes
    public function scopeOpen($query)
    {
        return $query->whereHas('status', function ($q) {
            $q->where('is_closed', false);
        });
    }

    public function scopeClosed($query)
    {
        return $query->whereHas('status', function ($q) {
            $q->where('is_closed', true);
        });
    }

    public function scopeUnassigned($query)
    {
        return $query->whereNull('assigned_to_id');
    }

    /**
     * Scope for tickets assigned to specific technician (individual user)
     */
    public function scopeForTechnician($query, $userId)
    {
        return $query->where('assigned_to_id', $userId);
    }

    public function scopeByRequester($query, $userId)
    {
        return $query->where('requester_id', $userId);
    }

    public function scopePendingApproval($query)
    {
        return $query->where('needs_approval', true)
            ->where('approval_status', 'pending');
    }

    // Helper Methods
    public function isOpen(): bool
    {
        return !$this->status->is_closed;
    }

    public function isClosed(): bool
    {
        return $this->status->is_closed;
    }

    public function isAssigned(): bool
    {
        return !is_null($this->assigned_to_id);
    }

    public function needsApproval(): bool
    {
        return $this->needs_approval && $this->approval_status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->approval_status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->approval_status === 'rejected';
    }

    /**
     * Assign ticket to a technician
     */
    public function assignTo(User $user): void
    {
        $this->update(['assigned_to_id' => $user->id]);
    }

    /**
     * Update ticket status
     */
    public function updateStatus(Status $status): void
    {
        $updates = ['status_id' => $status->id];
        
        if ($status->is_closed) {
            $updates['closed_at'] = now();
        }
        
        $this->update($updates);
    }

    /**
     * Mark ticket as resolved
     */
    public function resolve(string $notes = null): void
    {
        $this->update([
            'resolved_at' => now(),
            'resolution_notes' => $notes,
        ]);
    }

    /**
     * Approve ticket
     */
    public function approve(User $approver, string $notes = null): void
    {
        // Find "Open" status to set after approval
        $openStatus = Status::where('slug', 'open')->first();
        
        $this->update([
            'approval_status' => 'approved',
            'approved_by_id' => $approver->id,
            'approved_at' => now(),
            'approval_notes' => $notes,
            'status_id' => $openStatus ? $openStatus->id : $this->status_id,
            'needs_approval' => false,
        ]);
    }

    /**
     * Reject ticket
     */
    public function reject(User $approver, string $notes = null): void
    {
        // Find "Closed" status to set after rejection
        $closedStatus = Status::where('slug', 'closed')->first();
        
        $this->update([
            'approval_status' => 'rejected',
            'approved_by_id' => $approver->id,
            'approved_at' => now(),
            'approval_notes' => $notes,
            'status_id' => $closedStatus ? $closedStatus->id : $this->status_id,
        ]);
    }
}
