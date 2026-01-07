<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Priority extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'color',
        'level',
    ];

    protected $casts = [
        'level' => 'integer',
    ];

    /**
     * Boot function to auto-generate slug
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($priority) {
            if (empty($priority->slug)) {
                $priority->slug = Str::slug($priority->name);
            }
        });
    }

    /**
     * Get tickets with this priority
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Order by urgency level (highest first)
     */
    public function scopeByUrgency($query)
    {
        return $query->orderBy('level', 'desc');
    }
}
