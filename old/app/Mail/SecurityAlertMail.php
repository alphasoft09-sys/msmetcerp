<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SecurityAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public $email;
    public $ipAddress;
    public $userAgent;
    public $guard;
    public $attemptedPassword;
    public $failedAttempts;
    public $attemptedAt;

    /**
     * Create a new message instance.
     */
    public function __construct($email, $ipAddress, $userAgent, $guard, $attemptedPassword, $failedAttempts, $attemptedAt)
    {
        $this->email = $email;
        $this->ipAddress = $ipAddress;
        $this->userAgent = $userAgent;
        $this->guard = $guard;
        $this->attemptedPassword = $attemptedPassword;
        $this->failedAttempts = $failedAttempts;
        $this->attemptedAt = $attemptedAt;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🚨 SECURITY ALERT: Multiple Failed Login Attempts Detected - ' . env('PROJECT_NAME', 'MSME Technology Center'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.security-alert',
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
