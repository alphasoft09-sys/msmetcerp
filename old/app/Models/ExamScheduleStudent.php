<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamScheduleStudent extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_schedule_id',
        'student_roll_numbers',
    ];

    protected $casts = [
        'student_roll_numbers' => 'array',
    ];

    /**
     * Get the exam schedule that owns this student record
     */
    public function examSchedule()
    {
        return $this->belongsTo(ExamSchedule::class);
    }

    /**
     * Get all student roll numbers as array
     */
    public function getStudentRollNumbersArrayAttribute()
    {
        return $this->student_roll_numbers ?? [];
    }

    /**
     * Get student count
     */
    public function getStudentCountAttribute()
    {
        return count($this->student_roll_numbers ?? []);
    }

    /**
     * Check if a specific roll number exists
     */
    public function hasStudentRollNumber($rollNumber)
    {
        return in_array($rollNumber, $this->student_roll_numbers ?? []);
    }

    /**
     * Add a student roll number to the array
     */
    public function addStudentRollNumber($rollNumber)
    {
        $rollNumbers = $this->student_roll_numbers ?? [];
        if (!in_array($rollNumber, $rollNumbers)) {
            $rollNumbers[] = $rollNumber;
            $this->student_roll_numbers = $rollNumbers;
            $this->save();
        }
        return $this;
    }

    /**
     * Remove a student roll number from the array
     */
    public function removeStudentRollNumber($rollNumber)
    {
        $rollNumbers = $this->student_roll_numbers ?? [];
        $rollNumbers = array_filter($rollNumbers, function($rn) use ($rollNumber) {
            return $rn !== $rollNumber;
        });
        $this->student_roll_numbers = array_values($rollNumbers);
        $this->save();
        return $this;
    }
}
