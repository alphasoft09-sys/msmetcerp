<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class LmsMedia extends Model
{
    protected $fillable = [
        'lms_site_id',
        'media_type',
        'original_name',
        'file_name',
        'file_path',
        'file_url',
        'mime_type',
        'file_size',
        'alt_text',
        'description',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Get the LMS site that owns this media
     */
    public function lmsSite(): BelongsTo
    {
        return $this->belongsTo(TcLms::class, 'lms_site_id');
    }

    /**
     * Get the full URL for the media file
     */
    public function getFullUrlAttribute()
    {
        return Storage::url($this->file_path);
    }

    /**
     * Get formatted file size
     */
    public function getFormattedSizeAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Check if media is an image
     */
    public function isImage()
    {
        return $this->media_type === 'image';
    }

    /**
     * Check if media is a video
     */
    public function isVideo()
    {
        return $this->media_type === 'video';
    }

    /**
     * Get image dimensions from metadata
     */
    public function getImageDimensions()
    {
        if ($this->isImage() && isset($this->metadata['width']) && isset($this->metadata['height'])) {
            return [
                'width' => $this->metadata['width'],
                'height' => $this->metadata['height']
            ];
        }
        return null;
    }

    /**
     * Scope for images only
     */
    public function scopeImages($query)
    {
        return $query->where('media_type', 'image');
    }

    /**
     * Scope for videos only
     */
    public function scopeVideos($query)
    {
        return $query->where('media_type', 'video');
    }

    /**
     * Delete the file when model is deleted
     */
    protected static function boot()
    {
        parent::boot();
        
        static::deleting(function ($media) {
            if (Storage::exists($media->file_path)) {
                Storage::delete($media->file_path);
            }
        });
    }
}