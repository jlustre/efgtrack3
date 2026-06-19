<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Schema::defaultStringLength(191);

        Event::listen(
            \App\Events\Fna\FnaSubmittedForReview::class,
            [\App\Listeners\Fna\NotifyCfmOfFnaSubmission::class, 'handle'],
        );
        Event::listen(
            \App\Events\Fna\FnaSubmittedForReview::class,
            [\App\Listeners\Fna\CreateFnaWorkflowTasks::class, 'handleSubmitted'],
        );
        Event::listen(
            \App\Events\Fna\FnaApproved::class,
            [\App\Listeners\Fna\NotifyAssociateOfFnaApproval::class, 'handle'],
        );
        Event::listen(
            \App\Events\Fna\FnaApproved::class,
            [\App\Listeners\Fna\CreateFnaWorkflowTasks::class, 'handleApproved'],
        );
        Event::listen(
            \App\Events\Fna\FnaRevisionRequested::class,
            [\App\Listeners\Fna\NotifyAssociateOfFnaRevision::class, 'handle'],
        );
        Event::listen(
            \App\Events\Fna\FnaRevisionRequested::class,
            [\App\Listeners\Fna\CreateFnaWorkflowTasks::class, 'handleRevision'],
        );
        Event::listen(
            \App\Events\Fna\FnaSubmittedForReview::class,
            [\App\Listeners\Fna\SyncFnaProspectIntegration::class, 'handleSubmitted'],
        );
        Event::listen(
            \App\Events\Fna\FnaApproved::class,
            [\App\Listeners\Fna\SyncFnaProspectIntegration::class, 'handleApproved'],
        );
        Event::listen(
            \App\Events\Fna\FnaRevisionRequested::class,
            [\App\Listeners\Fna\SyncFnaProspectIntegration::class, 'handleRevision'],
        );
        Event::listen(
            \App\Events\Fna\FnaMeetingScheduled::class,
            [\App\Listeners\Fna\SyncFnaProspectIntegration::class, 'handleMeetingScheduled'],
        );
        Event::listen(
            \App\Events\Support\SupportTicketCreated::class,
            [\App\Listeners\Support\SendSupportTicketCreatedNotifications::class, 'handle'],
        );
        Event::listen(
            \App\Events\Support\SupportTicketStatusChanged::class,
            [\App\Listeners\Support\SendSupportTicketStatusChangedNotification::class, 'handle'],
        );
        Event::listen(
            \App\Events\Support\SupportTicketAgentReplied::class,
            [\App\Listeners\Support\SendSupportTicketAgentReplyNotification::class, 'handle'],
        );
    }
}
