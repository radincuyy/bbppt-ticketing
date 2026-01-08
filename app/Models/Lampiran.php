<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Lampiran extends Model
{
    protected $table = 'lampiran';
    protected $primaryKey = 'id_lampiran';

    protected $fillable = [
        'id_tiket',
        'nama_file',
        'path_file',
        'tipe_file',
    ];

    /**
     * Get tiket yang memiliki lampiran ini
     */
    public function tiket(): BelongsTo
    {
        return $this->belongsTo(Tiket::class, 'id_tiket', 'id_tiket');
    }

    /**
     * Get URL file
     */
    public function getUrlAttribute(): string
    {
        return Storage::url($this->path_file);
    }

    /**
     * Check apakah file adalah gambar
     */
    public function isImage(): bool
    {
        return in_array($this->tipe_file, ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
    }

    /**
     * Check apakah file adalah PDF
     */
    public function isPdf(): bool
    {
        return $this->tipe_file === 'application/pdf';
    }

    /**
     * Hapus file dari storage
     */
    public function hapusFile(): bool
    {
        return Storage::delete($this->path_file);
    }
}
