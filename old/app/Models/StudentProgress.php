<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentProgress extends Model
{
    use HasFactory;

    protected $table = 'student_progress';

    protected $fillable = [
        'student_id',
        'subject_id',
        'faculty_id',
        'tc_code',
        'assessment_type',
        'title',
        'score',
        'max_score',
        'assessment_date',
        'comments',
        'grade'
    ];

    protected $casts = [
        'assessment_date' => 'date',
        'score' => 'decimal:2',
        'max_score' => 'decimal:2',
    ];

    /**
     * Get the student for this progress record
     */
    public function student()
    {
        return $this->belongsTo(StudentLogin::class, 'student_id');
    }

    /**
     * Get the subject for this progress record
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the faculty for this progress record
     */
    public function faculty()
    {
        return $this->belongsTo(User::class, 'faculty_id');
    }

    /**
     * Scope to get progress by student
     */
    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope to get progress by subject
     */
    public function scopeBySubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    /**
     * Scope to get progress by faculty
     */
    public function scopeByFaculty($query, $facultyId)
    {
        return $query->where('faculty_id', $facultyId);
    }

    /**
     * Scope to get progress by assessment type
     */
    public function scopeByAssessmentType($query, $type)
    {
        return $query->where('assessment_type', $type);
    }

    /**
     * Get percentage score
     */
    public function getPercentageAttribute()
    {
        if ($this->max_score > 0) {
            return round(($this->score / $this->max_score) * 100, 2);
        }
        return 0;
    }

    /**
     * Get grade based on percentage
     */
    public function getCalculatedGradeAttribute()
    {
        $percentage = $this->percentage;
        
        if ($percentage >= 90) return 'A';
        if ($percentage >= 80) return 'B';
        if ($percentage >= 70) return 'C';
        if ($percentage >= 60) return 'D';
        return 'F';
    }

    /**
     * Get grade color class
     */
    public function getGradeColorClassAttribute()
    {
        $grade = $this->grade ?: $this->calculated_grade;
        
        return match($grade) {
            'A' => 'text-success',
            'B' => 'text-primary',
            'C' => 'text-warning',
            'D' => 'text-info',
            'F' => 'text-danger',
            default => 'text-secondary'
        };
    }
}
