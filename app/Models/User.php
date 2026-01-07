<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'jabatan',
        'phone',
        'is_active',
        'last_login_at',
        'last_logout_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'last_logout_at' => 'datetime',
        ];
    }

    /**
     * Activity log options
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'jabatan', 'is_active'])
            ->logOnlyDirty();
    }

    /**
     * Get tickets created by this user (as requester)
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'requester_id');
    }

    /**
     * Get tickets assigned to this user (as technician)
     */
    public function assignedTickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'assigned_to_id');
    }

    /**
     * Get comments by this user
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Check if user is a staff member (not requester)
     */
    public function isStaff(): bool
    {
        return $this->hasAnyRole(['Helpdesk', 'Technician', 'TeamLead', 'ManagerTI']);
    }

    /**
     * Check if user can manage tickets
     */
    public function canManageTickets(): bool
    {
        return $this->hasAnyRole(['Helpdesk', 'TeamLead', 'ManagerTI']);
    }

    /**
     * Check if user can approve tickets
     */
    public function canApproveTickets(): bool
    {
        return $this->hasRole('ManagerTI');
    }
}
