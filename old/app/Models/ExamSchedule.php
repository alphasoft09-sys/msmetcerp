<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamSchedule extends Model
{
    protected $fillable = [
        'created_by',
        'tc_code',
        'course_name',
        'batch_code',
        'semester',
        'exam_type',
        'exam_coordinator',
        'exam_start_date',
        'exam_end_date',
        'program_number',
        'centre_id',
        'status',
        'current_stage',
        'comment',
        'held_by',
        'rejected_by',
        'approved_by',
        'rejected_at',
        'approved_at',
        'held_at',
        'course_completion_file',
        'student_details_file',
        'terms_accepted',
        'file_no',
    ];

    protected $casts = [
        'exam_start_date' => 'date',
        'exam_end_date' => 'date',
        'terms_accepted' => 'boolean',
        'held_by' => 'integer',
        'rejected_by' => 'integer',
        'approved_by' => 'integer',
        'rejected_at' => 'datetime',
        'approved_at' => 'datetime',
        'held_at' => 'datetime',
    ];

    /**
     * Get the faculty who created this exam schedule
     */
    public function faculty(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who held this exam schedule
     */
    public function heldByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'held_by');
    }

    /**
     * Get the user who rejected this exam schedule
     */
    public function rejectedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    /**
     * Get the user who approved this exam schedule
     */
    public function approvedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the students for this exam schedule
     */
    public function students(): HasMany
    {
        return $this->hasMany(ExamScheduleStudent::class);
    }

    /**
     * Get all student roll numbers for this exam schedule
     */
    public function getStudentRollNumbersAttribute()
    {
        $studentRecord = $this->students()->first();
        return $studentRecord ? $studentRecord->student_roll_numbers : [];
    }

    /**
     * Get student count for this exam schedule
     */
    public function getStudentCountAttribute()
    {
        $studentRecord = $this->students()->first();
        return $studentRecord ? $studentRecord->student_count : 0;
    }

    /**
     * Check if a specific student roll number exists in this exam schedule
     */
    public function hasStudentRollNumber($rollNumber)
    {
        $studentRecord = $this->students()->first();
        return $studentRecord ? $studentRecord->hasStudentRollNumber($rollNumber) : false;
    }

    /**
     * Add a student roll number to this exam schedule
     */
    public function addStudentRollNumber($rollNumber)
    {
        $studentRecord = $this->students()->first();
        if (!$studentRecord) {
            $studentRecord = $this->students()->create([
                'student_roll_numbers' => []
            ]);
        }
        return $studentRecord->addStudentRollNumber($rollNumber);
    }

    /**
     * Remove a student roll number from this exam schedule
     */
    public function removeStudentRollNumber($rollNumber)
    {
        $studentRecord = $this->students()->first();
        if ($studentRecord) {
            return $studentRecord->removeStudentRollNumber($rollNumber);
        }
        return $this;
    }

    /**
     * Set all student roll numbers for this exam schedule
     */
    public function setStudentRollNumbers($rollNumbers)
    {
        // Delete existing student records
        $this->students()->delete();
        
        // Create new record with all roll numbers
        if (!empty($rollNumbers)) {
            $this->students()->create([
                'student_roll_numbers' => $rollNumbers
            ]);
        }
        
        return $this;
    }

    /**
     * Get the modules for this exam schedule
     */
    public function modules(): HasMany
    {
        return $this->hasMany(ExamScheduleModule::class);
    }

    /**
     * Get the qualification for this exam schedule
     */
    public function qualification(): BelongsTo
    {
        return $this->belongsTo(Qualification::class, 'course_name', 'qf_name');
    }

    /**
     * Get the centre for this exam schedule
     */
    public function centre(): BelongsTo
    {
        return $this->belongsTo(TcCentre::class, 'centre_id');
    }

    /**
     * Scope to get schedules by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get schedules by current stage
     */
    public function scopeByStage($query, $stage)
    {
        return $query->where('current_stage', $stage);
    }

    /**
     * Scope to get schedules by TC code
     */
    public function scopeByTcCode($query, $tcCode)
    {
        return $query->where('tc_code', $tcCode);
    }

    /**
     * Get formatted exam start date for HTML input
     */
    public function getExamStartDateForInputAttribute()
    {
        return $this->exam_start_date ? $this->exam_start_date->format('Y-m-d') : '';
    }

    /**
     * Get the exam end date formatted for input fields
     */
    public function getExamEndDateForInputAttribute()
    {
        return $this->exam_end_date ? $this->exam_end_date->format('Y-m-d') : '';
    }

    /**
     * Get the course completion file URL
     */
    public function getCourseCompletionFileUrlAttribute()
    {
        if (!$this->course_completion_file) {
            return null;
        }
        
        // Extract filename from the stored path
        $filename = basename($this->course_completion_file);
        return route('files.exam-schedules.course-completion', $filename);
    }

    /**
     * Get the student details file URL
     */
    public function getStudentDetailsFileUrlAttribute()
    {
        if (!$this->student_details_file) {
            return null;
        }
        
        // Extract filename from the stored path
        $filename = basename($this->student_details_file);
        return route('files.exam-schedules.student-details', $filename);
    }
}
