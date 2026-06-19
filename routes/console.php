<?php

use App\Jobs\Fna\RollupFnaAnalytics;
use App\Jobs\Goals\DispatchGoalReminders;
use App\Jobs\Goals\GenerateGoalScorecards;
use App\Jobs\Goals\RollupGoalProgress;
use App\Jobs\Goals\SendGoalPerformanceReports;
use App\Jobs\Support\CheckSupportTicketSlaJob;
use App\Services\Goals\GoalAlertService;
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

Schedule::job(RollupGoalProgress::class)->daily();
Schedule::job(DispatchGoalReminders::class)->hourly();
Schedule::job(GenerateGoalScorecards::class)->weeklyOn(1, '06:00');
Schedule::job(new SendGoalPerformanceReports('weekly'))->weeklyOn(1, '08:00');
Schedule::job(new SendGoalPerformanceReports('monthly'))->monthlyOn(1, '08:00');
Schedule::job(new SendGoalPerformanceReports('quarterly'))->cron('0 8 1 1,4,7,10 *');
Schedule::job(CheckSupportTicketSlaJob::class)->hourly();
