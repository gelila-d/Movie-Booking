<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LoginAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $ipAddress;
    public string $userAgent;
    public string $loginTime;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $ipAddress, string $userAgent, string $loginTime)
    {
        $this->user = $user;
        $this->ipAddress = $ipAddress;
        $this->userAgent = $userAgent;
        $this->loginTime = $loginTime;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Security Alert: New Sign-In to Your Account 🔒',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.login-alert',
            with: [
                'name' => $this->user->name,
                'email' => $this->user->email,
                'ipAddress' => $this->ipAddress,
                'userAgent' => $this->userAgent,
                'loginTime' => $this->loginTime,
            ],
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
