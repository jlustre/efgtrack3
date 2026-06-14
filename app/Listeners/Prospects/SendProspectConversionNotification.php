<?php

namespace App\Listeners\Prospects;

use App\Events\Prospects\ProspectConverted;
use App\Notifications\ProspectConversionNotification;

class SendProspectConversionNotification
{
    public function handle(ProspectConverted $event): void
    {
        $owner = $event->prospect->owner;

        if (! $owner) {
            return;
        }

        $invitationUrl = $event->phase === 'initiated' && $event->invitation
            ? $event->invitation->invitationUrl()
            : null;

        $owner->notify(new ProspectConversionNotification(
            $event->prospect,
            $event->conversion,
            $event->phase,
            $invitationUrl,
        ));
    }
}
