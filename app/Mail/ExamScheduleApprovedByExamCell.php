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

class ExamScheduleApprovedByExamCell extends Mailable
{
    use Queueable, SerializesModels;

    public $examSchedule;
    public $examCellUser;
    public $tcHeadUser;

    /**
     * Create a new message instance.
     */
    public function __construct(ExamSchedule $examSchedule, User $examCellUser, User $tcHeadUser)
    {
        $this->examSchedule = $examSchedule;
        $this->examCellUser = $examCellUser;
        $this->tcHeadUser = $tcHeadUser;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Exam Schedule Approved by Exam Cell - ' . $this->examSchedule->course_name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.exam-schedule-approved-by-exam-cell',
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