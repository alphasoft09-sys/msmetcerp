<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'tc_code',
        'faculty_id',
        'class_level',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the faculty that teaches this subject
     */
    public function faculty()
    {
        return $this->belongsTo(User::class, 'faculty_id');
    }

    /**
     * Get the class schedules for this subject
     */
    public function classSchedules()
    {
        return $this->hasMany(ClassSchedule::class);
    }

    /**
     * Get the attendances for this subject
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get the student progress records for this subject
     */
    public function studentProgress()
    {
        return $this->hasMany(StudentProgress::class);
    }

    /**
     * Scope to get active subjects
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get subjects by TC code
     */
    public function scopeByTcCode($query, $tcCode)
    {
        return $query->where('tc_code', $tcCode);
    }

    /**
     * Scope to get subjects by faculty
     */
    public function scopeByFaculty($query, $facultyId)
    {
        return $query->where('faculty_id', $facultyId);
    }

    /**
     * Scope to get subjects by class level
     */
    public function scopeByClassLevel($query, $classLevel)
    {
        return $query->where('class_level', $classLevel);
    }
}
