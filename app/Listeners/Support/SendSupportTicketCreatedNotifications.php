<?php

declare(strict_types=1);

namespace App\Listeners\Support;

use App\Events\Support\SupportTicketCreated;
use App\Models\User;
use App\Notifications\Support\SupportTicketCreatedNotification;
use App\Notifications\Support\SupportTicketUrgentAlertNotification;

class SendSupportTicketCreatedNotifications
{
    public function handle(SupportTicketCreated $event): void
    {
        $ticket = $event->ticket->loadMissing(['user', 'status']);

        $ticket->user?->notify(new SupportTicketCreatedNotification($ticket));

        if ($ticket->urgency !== 'urgent') {
            return;
        }

        User::role(['super-admin', 'admin', 'support-agent'])
            ->where('is_active', true)
            ->get()
            ->each(fn (User $admin) => $admin->notify(new SupportTicketUrgentAlertNotification($ticket)));
    }
}
