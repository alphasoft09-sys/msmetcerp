<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TcHeaderLayout extends Model
{
    protected $fillable = [
        'tc_id',
        'header_layout_url',
    ];

    /**
     * Get the TC (Tool Room) that owns this header layout
     */
    public function tc(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tc_id', 'from_tc');
    }

    /**
     * Get the full URL for the header layout image
     */
    public function getHeaderLayoutUrlAttribute($value)
    {
        if ($value) {
            // Extract filename from the path
            $filename = basename($value);
            
            // Try to use the route, fallback to direct URL if route doesn't exist
            try {
                return route('images.header-layouts', ['filename' => $filename]);
            } catch (\Exception $e) {
                // Fallback to direct storage URL
            return asset('storage/' . $value);
            }
        }
        return null;
    }

    /**
     * Get the storage path for the header layout image
     */
    public function getStoragePathAttribute()
    {
        return $this->attributes['header_layout_url'] ?? null;
    }
}
