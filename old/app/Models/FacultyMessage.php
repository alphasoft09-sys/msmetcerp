<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacultyMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'faculty_id',
        'student_id',
        'tc_code',
        'subject',
        'message',
        'message_type',
        'target_class',
        'is_read',
        'read_at'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * Get the faculty who sent this message
     */
    public function faculty()
    {
        return $this->belongsTo(User::class, 'faculty_id');
    }

    /**
     * Get the student who received this message
     */
    public function student()
    {
        return $this->belongsTo(StudentLogin::class, 'student_id');
    }

    /**
     * Scope to get messages by faculty
     */
    public function scopeByFaculty($query, $facultyId)
    {
        return $query->where('faculty_id', $facultyId);
    }

    /**
     * Scope to get messages by student
     */
    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope to get messages by TC code
     */
    public function scopeByTcCode($query, $tcCode)
    {
        return $query->where('tc_code', $tcCode);
    }

    /**
     * Scope to get messages by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('message_type', $type);
    }

    /**
     * Scope to get unread messages
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope to get read messages
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Mark message as read
     */
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }

    /**
     * Get message type display text
     */
    public function getMessageTypeDisplayAttribute()
    {
        return ucfirst($this->message_type);
    }

    /**
     * Get short message preview
     */
    public function getMessagePreviewAttribute()
    {
        return \Str::limit($this->message, 100);
    }
}
