<?php

declare(strict_types=1);

namespace App\Listeners\Support;

use App\Events\Support\SupportTicketStatusChanged;
use App\Notifications\Support\SupportTicketStatusChangedNotification;

class SendSupportTicketStatusChangedNotification
{
    public function handle(SupportTicketStatusChanged $event): void
    {
        $ticket = $event->ticket->loadMissing(['user', 'status']);

        if ($ticket->user_id === $event->actor->id) {
            return;
        }

        $ticket->user?->notify(new SupportTicketStatusChangedNotification($ticket, $event->actor));
    }
}
