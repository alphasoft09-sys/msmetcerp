<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LmsDepartment extends Model
{
    protected $fillable = [
        'department_name',
        'department_slug',
        'description',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the admin who created this department
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all LMS sites for this department
     */
    public function lmsSites(): HasMany
    {
        return $this->hasMany(TcLms::class, 'site_department', 'department_name');
    }

    /**
     * Scope to get active departments
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get departments by creator
     */
    public function scopeByCreator($query, $userId)
    {
        return $query->where('created_by', $userId);
    }

    /**
     * Get the department URL
     */
    public function getDepartmentUrlAttribute()
    {
        return url("/lms/{$this->department_slug}");
    }

    /**
     * Generate unique department slug
     */
    public static function generateUniqueSlug($name)
    {
        $baseSlug = \Str::slug($name);
        $counter = 1;
        $slug = $baseSlug;
        
        while (static::where('department_slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }

    /**
     * Boot method to auto-generate slug
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($department) {
            if (empty($department->department_slug)) {
                $department->department_slug = static::generateUniqueSlug($department->department_name);
            }
        });
    }
}