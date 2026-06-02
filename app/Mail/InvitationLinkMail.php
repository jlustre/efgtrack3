<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class InvitationLinkMail extends Mailable
{
    public function __construct(
        public string $customSubject,
        public string $emailBody,
        public string $senderName,
        public string $senderEmail,
    ) {
        //
    }

    public function build(): self
    {
        return $this
            ->from($this->senderEmail, $this->senderName)
            ->replyTo($this->senderEmail, $this->senderName)
            ->subject($this->customSubject)
            ->text('emails.invitation-link');
    }
}
