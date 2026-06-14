<?php

namespace App\Listeners\Fna;

use App\Events\Fna\FnaRevisionRequested;
use App\Notifications\Fna\FnaRevisionRequestedNotification;

class NotifyAssociateOfFnaRevision
{
    public function handle(FnaRevisionRequested $event): void
    {
        $event->fna->loadMissing('owner');

        if ($event->fna->owner) {
            $event->fna->owner->notify(new FnaRevisionRequestedNotification(
                $event->fna,
                $event->reviewedBy,
                $event->comment,
            ));
        }
    }
}
