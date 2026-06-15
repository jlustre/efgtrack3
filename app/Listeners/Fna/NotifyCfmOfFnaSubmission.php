<?php

namespace App\Listeners\Fna;

use App\Events\Fna\FnaSubmittedForReview;
use App\Notifications\Fna\FnaSubmittedNotification;

class NotifyCfmOfFnaSubmission
{
    public function handle(FnaSubmittedForReview $event): void
    {
        if ($event->cfm) {
            $event->cfm->notify(new FnaSubmittedNotification($event->fna, $event->submittedBy));
        }
    }
}
