<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Status extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'color',
        'order',
        'is_default',
        'is_closed',
    ];

    protected $casts = [
        'order' => 'integer',
        'is_default' => 'boolean',
        'is_closed' => 'boolean',
    ];

    /**
     * Boot function to auto-generate slug
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($status) {
            if (empty($status->slug)) {
                $status->slug = Str::slug($status->name);
            }
        });
    }

    /**
     * Get tickets with this status
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Get the default status
     */
    public static function getDefault(): ?self
    {
        return static::where('is_default', true)->first();
    }

    /**
     * Scope for open statuses
     */
    public function scopeOpen($query)
    {
        return $query->where('is_closed', false);
    }

    /**
     * Scope for closed statuses
     */
    public function scopeClosed($query)
    {
        return $query->where('is_closed', true);
    }

    /**
     * Order by display order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
