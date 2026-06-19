<?php

declare(strict_types=1);

namespace App\Listeners\Support;

use App\Events\Support\SupportTicketAgentReplied;
use App\Notifications\Support\SupportTicketAgentReplyNotification;

class SendSupportTicketAgentReplyNotification
{
    public function handle(SupportTicketAgentReplied $event): void
    {
        $ticket = $event->ticket->loadMissing('user');

        if ($ticket->user_id === $event->agent->id) {
            return;
        }

        $ticket->user?->notify(new SupportTicketAgentReplyNotification($ticket, $event->comment));
    }
}
