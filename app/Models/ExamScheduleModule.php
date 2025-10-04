<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class ExamScheduleModule extends Model
{
    protected $fillable = [
        'exam_schedule_id',
        'nos_code',
        'is_theory',
        'venue',
        'invigilator',
        'exam_date',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'is_theory' => 'boolean',
        'exam_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    /**
     * Get the exam schedule this module belongs to
     */
    public function examSchedule(): BelongsTo
    {
        return $this->belongsTo(ExamSchedule::class);
    }

    /**
     * Get formatted start time for HTML input
     */
    public function getStartTimeForInputAttribute()
    {
        return $this->start_time ? $this->start_time->format('H:i') : '';
    }

    /**
     * Get formatted end time for HTML input
     */
    public function getEndTimeForInputAttribute()
    {
        return $this->end_time ? $this->end_time->format('H:i') : '';
    }

    /**
     * Get formatted exam date for HTML input
     */
    public function getExamDateForInputAttribute()
    {
        return $this->exam_date ? $this->exam_date->format('Y-m-d') : '';
    }
}
