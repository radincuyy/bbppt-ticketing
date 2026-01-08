<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prioritas extends Model
{
    protected $table = 'prioritas';
    protected $primaryKey = 'id_prioritas';

    protected $fillable = [
        'nama_prioritas',
        'color',
    ];

    /**
     * Get tiket dengan prioritas ini
     */
    public function tiket(): HasMany
    {
        return $this->hasMany(Tiket::class, 'id_prioritas', 'id_prioritas');
    }
}
