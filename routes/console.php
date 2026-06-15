<?php

use App\Jobs\Fna\RollupFnaAnalytics;
use App\Jobs\Prospects\RollupProspectAnalytics;
use App\Jobs\Prospects\RunProspectFollowUpEngine;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function (): void {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(RunProspectFollowUpEngine::class)->hourly();
Schedule::job(RollupProspectAnalytics::class)->daily();
Schedule::job(RollupFnaAnalytics::class)->daily();
