<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\ExamSchedule;
use App\Models\User;

class ExamScheduleSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public $examSchedule;
    public $faculty;
    public $examCellUser;

    /**
     * Create a new message instance.
     */
    public function __construct(ExamSchedule $examSchedule, User $faculty, User $examCellUser)
    {
        $this->examSchedule = $examSchedule;
        $this->faculty = $faculty;
        $this->examCellUser = $examCellUser;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Exam Schedule Submitted for Approval - ' . $this->examSchedule->course_name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.exam-schedule-submitted',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
} 