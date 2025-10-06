<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TcLms extends Model
{
    use SoftDeletes;
    
    protected $table = 'all_tc_lms';

    protected $fillable = [
        'tc_code',
        'faculty_code',
        'site_url',
        'site_department',
        'site_contents',
        'is_approved',
        'approved_by',
        'approved_at',
        'site_title',
        'site_description',
        'status',
        'can_edit_after_approval',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
        'can_edit_after_approval' => 'boolean',
        'rejected_at' => 'datetime',
        'deleted_at' => 'datetime',
        // site_contents is now stored as string (HTML content)
    ];

    /**
     * Get the faculty who created this LMS site
     */
    public function faculty(): BelongsTo
    {
        return $this->belongsTo(User::class, 'faculty_code', 'email');
    }

    /**
     * Get the admin who approved this LMS site
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the admin who rejected this LMS site
     */
    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    /**
     * Get the TC shot code information
     */
    public function tcShotCode(): BelongsTo
    {
        return $this->belongsTo(TcShotCode::class, 'tc_code', 'tc_code');
    }

    /**
     * Get all media files for this LMS site
     */
    public function media(): HasMany
    {
        return $this->hasMany(LmsMedia::class, 'lms_site_id');
    }

    /**
     * Get images for this LMS site
     */
    public function images(): HasMany
    {
        return $this->hasMany(LmsMedia::class, 'lms_site_id')->where('media_type', 'image');
    }

    /**
     * Get videos for this LMS site
     */
    public function videos(): HasMany
    {
        return $this->hasMany(LmsMedia::class, 'lms_site_id')->where('media_type', 'video');
    }

    /**
     * Scope to get approved sites
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope to get pending sites
     */
    public function scopePending($query)
    {
        return $query->where('is_approved', false)->where('status', 'submitted');
    }

    /**
     * Scope to get sites by department
     */
    public function scopeByDepartment($query, $department)
    {
        return $query->where('site_department', $department);
    }

    /**
     * Scope to get sites by TC code
     */
    public function scopeByTcCode($query, $tcCode)
    {
        return $query->where('tc_code', $tcCode);
    }

    /**
     * Scope to get sites by faculty
     */
    public function scopeByFaculty($query, $facultyCode)
    {
        return $query->where('faculty_code', $facultyCode);
    }

    /**
     * Get the public URL for the LMS site
     */
    public function getPublicUrlAttribute()
    {
        return url("/lms/{$this->site_department}/{$this->site_url}");
    }

    /**
     * Get the preview URL for the LMS site
     */
    public function getPreviewUrlAttribute()
    {
        return url("/lms/{$this->site_department}/{$this->site_url}/preview");
    }

    /**
     * Check if site is accessible publicly
     */
    public function isPubliclyAccessible()
    {
        return $this->is_approved && $this->status === 'approved';
    }

    /**
     * Check if site is in preview mode
     */
    public function isPreviewMode()
    {
        return !$this->is_approved || $this->status === 'draft';
    }

    /**
     * Get formatted site contents for display
     */
    public function getFormattedContentsAttribute()
    {
        // Return the HTML content directly
        return $this->site_contents ?? '';
    }

    /**
     * Generate unique site URL slug
     */
    public static function generateUniqueSiteUrl($title, $tcCode)
    {
        $baseSlug = \Str::slug($title);
        $counter = 1;
        $siteUrl = $baseSlug;
        
        while (static::where('site_url', $siteUrl)->exists()) {
            $siteUrl = $baseSlug . '-' . $counter;
            $counter++;
        }
        
        return $siteUrl;
    }

    /**
     * Check if the site can be edited by faculty
     */
    public function canBeEditedByFaculty()
    {
        // Can edit if it's in draft status
        if ($this->status === 'draft') {
            return true;
        }
        
        // Can edit if it's in submitted status (before approval)
        if ($this->status === 'submitted') {
            return true;
        }
        
        // Can edit if approved and admin has given permission
        if ($this->status === 'approved' && $this->can_edit_after_approval) {
            return true;
        }
        
        // Allow editing if not rejected (more permissive for faculty)
        if ($this->status !== 'rejected') {
            return true;
        }
        
        return false;
    }

    /**
     * Check if the site can be deleted by faculty
     */
    public function canBeDeletedByFaculty()
    {
        // Can only delete if it's in draft status
        return $this->status === 'draft';
    }

    /**
     * Scope to get only non-deleted sites
     */
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }

    /**
     * Scope to get only deleted sites
     */
    public function scopeDeleted($query)
    {
        return $query->whereNotNull('deleted_at');
    }
}