<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditTrail extends Model
{
    protected $table = 'audit_trail';
    protected $primaryKey = 'id_log';
    
    public $timestamps = false;

    protected $fillable = [
        'id_tiket',
        'id_pengguna',
        'aktivitas',
        'timestamp',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($log) {
            $log->timestamp = now();
        });
    }

    /**
     * Get tiket terkait
     */
    public function tiket(): BelongsTo
    {
        return $this->belongsTo(Tiket::class, 'id_tiket', 'id_tiket');
    }

    /**
     * Get pengguna yang melakukan aktivitas
     */
    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_pengguna', 'id');
    }

    /**
     * Catat aktivitas baru
     */
    public static function catat(int $idTiket, int $idPengguna, string $aktivitas): self
    {
        return static::create([
            'id_tiket' => $idTiket,
            'id_pengguna' => $idPengguna,
            'aktivitas' => $aktivitas,
        ]);
    }
}
