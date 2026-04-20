<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InternshipAbandonedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $intern;
    public $daysOfAbsence;

    /**
     * Create a new message instance.
     */
    public function __construct(User $intern, int $daysOfAbsence)
    {
        $this->intern = $intern;
        $this->daysOfAbsence = $daysOfAbsence;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Notification d\'abandon de stage - ManageX')
                    ->markdown('emails.interns.abandoned');
    }
}
