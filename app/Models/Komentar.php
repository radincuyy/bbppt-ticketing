<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Komentar extends Model
{
    protected $table = 'komentar';
    protected $primaryKey = 'id_komentar';
    
    // Disable default timestamps karena kita pakai nama custom
    public $timestamps = false;

    protected $fillable = [
        'id_tiket',
        'id_pengguna',
        'isi_komentar',
        'tanggal_kirim',
    ];

    protected $casts = [
        'tanggal_kirim' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($komentar) {
            $komentar->tanggal_kirim = now();
        });
    }

    /**
     * Get tiket yang memiliki komentar ini
     */
    public function tiket(): BelongsTo
    {
        return $this->belongsTo(Tiket::class, 'id_tiket', 'id_tiket');
    }

    /**
     * Get pengguna yang menulis komentar
     */
    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_pengguna', 'id');
    }
}
