<?php

namespace App\Listeners\Fna;

use App\Events\Fna\FnaApproved;
use App\Notifications\Fna\FnaApprovedNotification;

class NotifyAssociateOfFnaApproval
{
    public function handle(FnaApproved $event): void
    {
        $event->fna->loadMissing('owner');

        if ($event->fna->owner) {
            $event->fna->owner->notify(new FnaApprovedNotification($event->fna, $event->reviewedBy));
        }
    }
}
