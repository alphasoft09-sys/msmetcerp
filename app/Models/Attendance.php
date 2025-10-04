<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'subject_id',
        'faculty_id',
        'tc_code',
        'date',
        'status',
        'remarks'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Get the student for this attendance record
     */
    public function student()
    {
        return $this->belongsTo(StudentLogin::class, 'student_id');
    }

    /**
     * Get the subject for this attendance record
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the faculty for this attendance record
     */
    public function faculty()
    {
        return $this->belongsTo(User::class, 'faculty_id');
    }

    /**
     * Scope to get attendance by date
     */
    public function scopeByDate($query, $date)
    {
        return $query->where('date', $date);
    }

    /**
     * Scope to get attendance by subject
     */
    public function scopeBySubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    /**
     * Scope to get attendance by faculty
     */
    public function scopeByFaculty($query, $facultyId)
    {
        return $query->where('faculty_id', $facultyId);
    }

    /**
     * Scope to get attendance by TC code
     */
    public function scopeByTcCode($query, $tcCode)
    {
        return $query->where('tc_code', $tcCode);
    }

    /**
     * Scope to get attendance by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'present' => 'bg-success',
            'absent' => 'bg-danger',
            'late' => 'bg-warning',
            'excused' => 'bg-info',
            default => 'bg-secondary'
        };
    }

    /**
     * Get status display text
     */
    public function getStatusDisplayAttribute()
    {
        return ucfirst($this->status);
    }
}
