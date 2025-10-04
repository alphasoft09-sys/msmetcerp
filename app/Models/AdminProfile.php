<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminProfile extends Model
{
    protected $table = 'admin_profiles';
    
    protected $fillable = [
        'user_id',
        'profile_photo',
        'signature',
        'qualification',
        'contact_no',
        'dob',
        'category',
        'mother_tongue',
        'blood_group',
        'course_completed_from',
        'date_of_completion',
        'current_section',
        'designation',
        'date_of_joining',
        'address_permanent',
        'address_correspondence',
        'tot_done',
        'tot_certification_date',
        'tot_certificate_number',
        'qualification_id',
        'is_sme',
        'proficient_module_ids',
        'sme_qualification_ids',
                        'toa_done',
                'toa_certification_date',
                'toa_certificate_number',
                'toa_completed_at',
                'toa_version',
                'toa_notes',
    ];

    protected $casts = [
        'dob' => 'date',
        'date_of_completion' => 'date',
        'date_of_joining' => 'date',
        'tot_certification_date' => 'date',
        'tot_done' => 'boolean',
        'is_sme' => 'boolean',
        'proficient_module_ids' => 'array',
        'sme_qualification_ids' => 'array',
                        'toa_done' => 'boolean',
                'toa_certification_date' => 'date',
                'toa_completed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the profile
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the qualification for this profile
     */
    public function qualificationRelation(): BelongsTo
    {
        return $this->belongsTo(Qualification::class, 'qualification_id');
    }

    /**
     * Get the proficient modules
     */
    public function proficientModules()
    {
        $moduleIds = $this->proficient_module_ids;
        
        if (is_string($moduleIds)) {
            $moduleIds = json_decode($moduleIds, true);
        }
        
        if (!$moduleIds || !is_array($moduleIds)) {
            return collect();
        }
        
        return \App\Models\QualificationModule::whereIn('id', $moduleIds)->get();
    }

    /**
     * Get the SME qualifications
     */
    public function smeQualifications()
    {
        $qualificationIds = $this->sme_qualification_ids;
        
        if (is_string($qualificationIds)) {
            $qualificationIds = json_decode($qualificationIds, true);
        }
        
        if (!$qualificationIds || !is_array($qualificationIds)) {
            return collect();
        }
        
        return Qualification::whereIn('id', $qualificationIds)->get();
    }

    /**
     * Get proficient module IDs as array
     */
    public function getProficientModuleIdsArray()
    {
        $moduleIds = $this->proficient_module_ids;
        
        if (is_string($moduleIds)) {
            $moduleIds = json_decode($moduleIds, true);
        }
        
        return is_array($moduleIds) ? $moduleIds : [];
    }

    /**
     * Get SME qualification IDs as array
     */
    public function getSmeQualificationIdsArray()
    {
        $qualificationIds = $this->sme_qualification_ids;
        
        if (is_string($qualificationIds)) {
            $qualificationIds = json_decode($qualificationIds, true);
        }
        
        return is_array($qualificationIds) ? $qualificationIds : [];
    }

    /**
     * Get the full URL for the profile photo
     */
    public function getProfilePhotoUrlAttribute($value)
    {
        if ($this->profile_photo) {
            // Extract filename from the path
            $filename = basename($this->profile_photo);
            
            // Try to use the route, fallback to direct URL if route doesn't exist
            try {
                return route('images.admin-profiles', ['type' => 'photos', 'filename' => $filename]);
            } catch (\Exception $e) {
                // Fallback to direct storage URL
                return asset('storage/' . $this->profile_photo);
            }
        }
        return null;
    }

    /**
     * Get the full URL for the signature
     */
    public function getSignatureUrlAttribute($value)
    {
        if ($this->signature) {
            // Extract filename from the path
            $filename = basename($this->signature);
            
            // Try to use the route, fallback to direct URL if route doesn't exist
            try {
                return route('images.admin-profiles', ['type' => 'signatures', 'filename' => $filename]);
            } catch (\Exception $e) {
                // Fallback to direct storage URL
                return asset('storage/' . $this->signature);
            }
        }
        return null;
    }
}
