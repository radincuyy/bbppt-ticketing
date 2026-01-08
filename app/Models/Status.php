<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Status extends Model
{
    protected $table = 'status';
    protected $primaryKey = 'id_status';

    protected $fillable = [
        'nama_status',
        'color',
    ];

    /**
     * Get tiket dengan status ini
     */
    public function tiket(): HasMany
    {
        return $this->hasMany(Tiket::class, 'id_status', 'id_status');
    }
}
