<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public $userName;
    public $userType;

    /**
     * Create a new message instance.
     */
    public function __construct($token, $userName, $userType)
    {
        $this->token = $token;
        $this->userName = $userName;
        $this->userType = $userType;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $userTypeText = $this->userType === 'admin' ? 'Admin' : 'Student';
        return new Envelope(
            subject: env('PROJECT_NAME', 'MSME Technology Center') . ' - Password Reset Request',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $resetUrl = $this->userType === 'admin' 
            ? route('admin.password.reset') . '?token=' . $this->token
            : route('student.password.reset') . '?token=' . $this->token;

        return new Content(
            view: 'emails.password-reset',
            with: [
                'userName' => $this->userName,
                'resetUrl' => $resetUrl,
                'userType' => $this->userType,
                'projectName' => env('PROJECT_NAME', 'MSME Technology Center')
            ]
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