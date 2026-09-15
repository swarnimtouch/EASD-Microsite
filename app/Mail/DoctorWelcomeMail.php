<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DoctorWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $doctor)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Welcome to PULCE Connect 2026');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.doctor-welcome');
    }
}
