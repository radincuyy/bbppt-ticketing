<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     * Sesuai class diagram: ID Pengguna, Nama Lengkap, Email, Jabatan, Login, Logout
     */
    protected $fillable = [
        'name',          // Nama Lengkap
        'email',         // Email
        'password',
        'jabatan',       // Jabatan
        'login',         // Waktu Login terakhir
        'logout',        // Waktu Logout terakhir
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'login' => 'datetime',
            'logout' => 'datetime',
        ];
    }

    /**
     * Get tiket yang dibuat oleh pengguna ini (sebagai pemohon)
     */
    public function tiket(): HasMany
    {
        return $this->hasMany(Tiket::class, 'id_pengguna', 'id');
    }

    /**
     * Get tiket yang ditugaskan ke pengguna ini (sebagai teknisi)
     */
    public function tiketDitugaskan(): HasMany
    {
        return $this->hasMany(Tiket::class, 'id_teknisi', 'id');
    }

    /**
     * Get komentar oleh pengguna ini
     */
    public function komentar(): HasMany
    {
        return $this->hasMany(Komentar::class, 'id_pengguna', 'id');
    }

    /**
     * Get audit trail oleh pengguna ini
     */
    public function auditTrail(): HasMany
    {
        return $this->hasMany(AuditTrail::class, 'id_pengguna', 'id');
    }

    /**
     * Check apakah pengguna adalah staff (bukan pemohon biasa)
     */
    public function isStaff(): bool
    {
        return $this->hasAnyRole(['Helpdesk', 'Technician', 'TeamLead', 'ManagerTI']);
    }

    /**
     * Check apakah pengguna bisa mengelola tiket
     */
    public function canManageTickets(): bool
    {
        return $this->hasAnyRole(['Helpdesk', 'TeamLead', 'ManagerTI']);
    }

    /**
     * Check apakah pengguna bisa approve tiket
     */
    public function canApproveTickets(): bool
    {
        return $this->hasRole('ManagerTI');
    }

    /**
     * Catat waktu login
     */
    public function catatLogin(): void
    {
        $this->update(['login' => now()]);
    }

    /**
     * Catat waktu logout
     */
    public function catatLogout(): void
    {
        $this->update(['logout' => now()]);
    }
}
