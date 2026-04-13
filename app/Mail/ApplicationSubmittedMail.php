<?php

namespace App\Mail;

use App\Models\IncubationApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicationSubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $application;
    public $applicantName;
    public $callTitle;
    public $applicationNumber;
    public $companyName;
    public $applicantEmail;
    public $submittedDate;

    /**
     * Create a new message instance.
     */
    public function __construct(IncubationApplication $application)
    {
        $this->application = $application;
        $this->applicantName = $application->applicant_name;
        $this->callTitle = $application->call->title ?? 'Incubation Program';
        $this->applicationNumber = $application->application_number;
        $this->companyName = $application->company_name;
        $this->applicantEmail = $application->applicant_email;
        $this->submittedDate = $application->submitted_at->format('F j, Y, g:i a');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Application Submitted - ' . $this->applicationNumber,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.application-submitted',
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