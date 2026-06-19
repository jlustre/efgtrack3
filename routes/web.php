<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\AssociateParticipationAgreementController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\AdminManagementController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CfmAssignmentConfirmationController;
use App\Http\Controllers\CfmManagementController;
use App\Http\Controllers\CfmPortalController;
use App\Http\Controllers\CfmTraineeChecklistController;
use App\Http\Controllers\ChecklistTypeStartController;
use App\Http\Controllers\DownlineController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FnaManagementController;
use App\Http\Controllers\ProspectActivityController;
use App\Http\Controllers\ProspectManagementController;
use App\Http\Controllers\ResourceDocumentsController;
use App\Http\Controllers\ResourceLinksController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TrackerChecklistController;
use App\Http\Controllers\TrainingController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\TrainingAchievementsController;
use App\Http\Controllers\TrainingAssignmentController;
use App\Http\Controllers\TrainingCertificationController;
use App\Http\Controllers\TrainingPathController;
use App\Http\Controllers\TrainingPlanController;
use App\Http\Controllers\Admin\AdminTrainingController;
use App\Http\Controllers\TrainingReportController;
use App\Http\Controllers\TrainingCoachingController;
use App\Http\Controllers\TrainingSessionController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/run-migrations-secret-2026', function () {
    abort_unless(app()->environment(['staging']), 403);

    Artisan::call('migrate', ['--force' => true]);
    Artisan::call('db:seed', ['--force' => true]);

    return nl2br(Artisan::output());
});

Route::get('/', function () {
    return view('welcome');
});

Route::get('/book/invite/{token}', [BookingController::class, 'invite'])->name('bookings.invite');
Route::get('/book/mentor/{mentorSlug}', [BookingController::class, 'publicPage'])->name('bookings.mentor');
Route::get('/book/{username}/{eventTypeSlug?}', [BookingController::class, 'publicPage'])->name('bookings.public');

Route::prefix('fna/client')->name('fna.client.')->group(function (): void {
    Route::get('/invite/{token}', \App\Livewire\Fna\Client\FnaClientPortalGate::class)->name('invite');
    Route::get('/invite/{token}/wizard', \App\Livewire\Fna\Client\FnaClientPortalWizard::class)->name('wizard');
    Route::get('/return', \App\Livewire\Fna\Client\FnaClientPortalReturn::class)->name('return');
});

Route::get('/cfm/assignments/{assignment}/confirm', [CfmAssignmentConfirmationController::class, 'show'])
    ->middleware('signed')
    ->name('cfm.assignments.confirm');

Route::middleware(['auth', 'verified', 'active'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('permission:view dashboard')
        ->name('dashboard');
    Route::get('/dashboard/stats/{type}/members', [DashboardController::class, 'statDetails'])
        ->middleware('permission:view dashboard')
        ->name('dashboard.stat-details');


    Route::view('/messages', 'messages.index')->name('messages.index');

    Route::view('/search', 'search.index')->name('search.index');
    Route::get('/onboarding', [OnboardingController::class, 'index'])->name('onboarding.index');
    Route::patch('/onboarding/{step}', [OnboardingController::class, 'update'])->name('onboarding.update');
    Route::patch('/onboarding-progress/{progress}/review', [OnboardingController::class, 'review'])->name('onboarding.review');
    Route::get('/licensing', [TrackerChecklistController::class, 'licensing'])->name('licensing.index');
    Route::patch('/licensing/{step}', [TrackerChecklistController::class, 'updateLicensing'])->name('licensing.update');
    Route::patch('/licensing-progress/{progress}/review', [TrackerChecklistController::class, 'reviewLicensing'])->name('licensing.review');
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::get('/field-apprenticeship', [TrackerChecklistController::class, 'apprenticeship'])->name('apprenticeship.index');
    Route::patch('/field-apprenticeship/{step}', [TrackerChecklistController::class, 'updateApprenticeship'])->name('apprenticeship.update');
    Route::patch('/field-apprenticeship-progress/{progress}/review', [TrackerChecklistController::class, 'reviewApprenticeship'])->name('apprenticeship.review');
    Route::get('/cfm-training', [TrackerChecklistController::class, 'cfmTraining'])->name('cfm-training.index');
    Route::patch('/cfm-training/{step}', [TrackerChecklistController::class, 'updateCfmTraining'])->name('cfm-training.update');
    Route::patch('/cfm-training-progress/{progress}/review', [TrackerChecklistController::class, 'reviewCfmTraining'])->name('cfm-training.review');
    Route::get('/cfm/portal', [CfmPortalController::class, 'index'])->middleware('cfm.portal')->name('cfm.portal');
    Route::patch('/cfm/portal/profile', [CfmPortalController::class, 'updateProfile'])->middleware('cfm.portal')->name('cfm.portal.profile.update');
    Route::patch('/cfm/portal/calendar-sharing', [CfmPortalController::class, 'updateCalendarSharing'])->middleware('cfm.portal')->name('cfm.portal.calendar-sharing.update');
    Route::post('/cfm/portal/assignments/{assignment}/confirm', [CfmPortalController::class, 'confirmAssignment'])->middleware('cfm.portal')->name('cfm.portal.assignments.confirm');
    Route::post('/cfm/portal/assignments/{assignment}/first-contact', [CfmPortalController::class, 'sendFirstContact'])->middleware('cfm.portal')->name('cfm.portal.assignments.first-contact');
    Route::get('/cfm/portal/trainees/{assignment}/checklist', [CfmTraineeChecklistController::class, 'show'])->middleware('cfm.portal')->name('cfm.portal.trainees.checklist');
    Route::patch('/cfm/portal/trainees/{assignment}/checklist/{item}', [CfmTraineeChecklistController::class, 'update'])->middleware('cfm.portal')->name('cfm.portal.trainees.checklist.update');
    Route::view('/training', 'training.index')->name('training.index');
    Route::get('/training/courses/{module:slug}', [TrainingController::class, 'course'])->name('training.courses.show');
    Route::get('/training/courses/{module:slug}/lessons/{lesson}', [TrainingController::class, 'lesson'])->name('training.lessons.show');
    Route::get('/training/assignments', [TrainingAssignmentController::class, 'index'])->name('training.assignments.index');
    Route::get('/training/assignments/manage', [TrainingAssignmentController::class, 'manage'])->name('training.assignments.manage')->middleware('permission:manage training');
    Route::get('/training/certifications', [TrainingCertificationController::class, 'index'])->name('training.certifications.index');
    Route::get('/training/certifications/reviews', [TrainingCertificationController::class, 'reviews'])->name('training.certifications.reviews');
    Route::get('/training/certifications/{userCertification}', [TrainingCertificationController::class, 'show'])->name('training.certifications.show');
    Route::get('/training/paths', [TrainingPathController::class, 'index'])->name('training.paths.index');
    Route::get('/training/paths/{path:code}', [TrainingPathController::class, 'show'])->name('training.paths.show');
    Route::get('/training/coaching', [TrainingCoachingController::class, 'index'])->name('training.coaching.index');
    Route::get('/training/sessions', [TrainingSessionController::class, 'index'])->name('training.sessions.index');
    Route::get('/training/sessions/{session}', [TrainingSessionController::class, 'show'])->name('training.sessions.show');
    Route::get('/training/achievements', [TrainingAchievementsController::class, 'index'])->name('training.achievements.index');
    Route::get('/training/plan', [TrainingPlanController::class, 'index'])->name('training.plan.index');
    Route::get('/training/reports', [TrainingReportController::class, 'index'])->name('training.reports.index');
    Route::get('/training/reports/download', [TrainingReportController::class, 'download'])->name('training.reports.download');
    Route::post('/training/reports/email', [TrainingReportController::class, 'email'])->name('training.reports.email');
    Route::view('/goals', 'goals.index')->middleware('permission:manage goals')->name('goals.index');
    Route::view('/goals/plan', 'goals.plan')->middleware('permission:manage goals')->name('goals.plan');
    Route::view('/goals/create', 'goals.create')->middleware('permission:manage goals')->name('goals.create');
    Route::get('/goals/blueprint/{blueprint}', fn (\App\Models\GoalBlueprint $blueprint) => view('goals.blueprint.show', compact('blueprint')))->middleware('permission:manage goals')->name('goals.blueprint.show');
    Route::view('/goals/what-if', 'goals.what-if')->middleware('permission:manage goals')->name('goals.what-if');
    Route::view('/goals/scorecard', 'goals.scorecard')->middleware('permission:manage goals')->name('goals.scorecard');
    Route::view('/goals/settings', 'goals.settings')->middleware('permission:manage goals')->name('goals.settings');
    Route::view('/goals/team', 'goals.team')->middleware('permission:view team goals')->name('goals.team');
    Route::view('/goals/coaching', 'goals.coaching')->middleware('permission:coach goals')->name('goals.coaching');
    Route::view('/goals/reports', 'goals.reports')->middleware('permission:manage goals')->name('goals.reports');
    Route::get('/goals/reports/download', [\App\Http\Controllers\GoalReportController::class, 'download'])->middleware('permission:manage goals')->name('goals.reports.download');
    Route::post('/goals/reports/email', [\App\Http\Controllers\GoalReportController::class, 'email'])->middleware('permission:manage goals')->name('goals.reports.email');
    Route::get('/assessments', [AssessmentController::class, 'index'])->name('assessments.index');
    Route::get('/assessments/{assessment}', [AssessmentController::class, 'show'])->name('assessments.show');
    Route::get('/assessments/{assessment}/take', [AssessmentController::class, 'take'])->name('assessments.take');
    Route::get('/assessments/{assessment}/attempts/{attempt}', [AssessmentController::class, 'result'])->name('assessments.attempts.show');
    Route::view('/rank-advancement', 'rank-advancement.index')->name('rank-advancement.index');
    Route::get('/team', [DownlineController::class, 'index'])->middleware('permission:view own team')->name('team.index');
    Route::get('/team/tree', [DownlineController::class, 'tree'])->middleware('permission:view team tree')->name('team.tree');
    Route::get('/team/tree/search', [DownlineController::class, 'treeSearch'])->middleware('permission:view team tree')->name('team.tree.search');
    Route::get('/team/org-chart', [DownlineController::class, 'orgChart'])->middleware('permission:view org chart')->name('team.org-chart');
    Route::get('/team/table', [DownlineController::class, 'table'])->middleware('permission:view team table')->name('team.table');
    Route::get('/team/hierarchy', [DownlineController::class, 'hierarchyTable'])->middleware('permission:view team tree')->name('team.hierarchy');
    Route::get('/team/member/{user}', [DownlineController::class, 'member'])->middleware('permission:view own team')->name('team.member');
    Route::post('/team/member/{user}/checklist-types/{typeCode}/start', [ChecklistTypeStartController::class, 'store'])->middleware('permission:view own team')->name('team.member.checklist-type.start');
    Route::get('/team/member/{user}/profile', [ProfileController::class, 'showMember'])->middleware('permission:view own team')->name('team.member.profile');
    Route::get('/team/member/{user}/tree', [DownlineController::class, 'tree'])->middleware('permission:view team tree')->name('team.member.tree');
    Route::get('/team/member/{user}/hierarchy', [DownlineController::class, 'hierarchyTable'])->middleware('permission:view team tree')->name('team.member.hierarchy');
    Route::get('/team/member/{user}/org-chart', [DownlineController::class, 'orgChart'])->middleware('permission:view org chart')->name('team.member.org-chart');
    Route::get('/team/export', [DownlineController::class, 'export'])->middleware('permission:export team data')->name('team.export');
    Route::view('/team/directs', 'team.directs')->middleware('permission:view team')->name('team.directs');
    Route::view('/team/trainees', 'team.trainees')->middleware('permission:view team')->name('team.trainees');
    Route::get('/team/cfms', [CfmManagementController::class, 'index'])->middleware('cfm.management')->name('team.cfms');
    Route::patch('/team/cfms/{user}/licensed-jurisdictions', [CfmManagementController::class, 'updateLicensedJurisdictions'])->middleware('cfm.management')->name('team.cfms.licensed-jurisdictions.update');
    Route::post('/team/cfms/assign', [CfmManagementController::class, 'assign'])->middleware('cfm.management')->name('team.cfms.assign');
    Route::post('/team/cfms', [CfmManagementController::class, 'store'])->middleware('cfm.management')->name('team.cfms.store');
    Route::view('/team/downlines', 'team.downlines')->middleware('permission:view team')->name('team.downlines');
    Route::get('/team/prospects', [ProspectManagementController::class, 'index'])->middleware('permission:manage prospects')->name('team.prospects');
    Route::get('/team/prospects/create', [ProspectManagementController::class, 'create'])->middleware('permission:manage prospects')->name('team.prospects.create');
    Route::get('/team/prospects/records/{prospect}', [ProspectManagementController::class, 'show'])->middleware('permission:manage prospects')->name('team.prospects.records.show');
    Route::get('/team/prospects/records/{prospect}/activity', [ProspectManagementController::class, 'activity'])->middleware('permission:manage prospects')->name('team.prospects.records.activity');
    Route::get('/team/prospects/records/{prospect}/edit', [ProspectManagementController::class, 'edit'])->middleware('permission:manage prospects')->name('team.prospects.records.edit');
    Route::patch('/team/prospects/records/{prospect}', [ProspectManagementController::class, 'update'])->middleware('permission:manage prospects')->name('team.prospects.records.update');
    Route::patch('/team/prospects/records/{prospect}/archive', [ProspectManagementController::class, 'archive'])->middleware('permission:manage prospects')->name('team.prospects.records.archive');
    Route::delete('/team/prospects/records/{prospect}', [ProspectManagementController::class, 'destroy'])->middleware('permission:manage prospects')->name('team.prospects.records.destroy');
    Route::get('/team/prospects/records/{prospect}/activities', [ProspectActivityController::class, 'index'])->middleware('permission:manage prospects')->name('team.prospects.activities.index');
    Route::post('/team/prospects/records/{prospect}/activities', [ProspectActivityController::class, 'store'])->middleware('permission:manage prospects')->name('team.prospects.activities.store');
    Route::patch('/team/prospects/records/{prospect}/activities/{activity}', [ProspectActivityController::class, 'update'])->middleware('permission:manage prospects')->name('team.prospects.activities.update');
    Route::delete('/team/prospects/records/{prospect}/activities/{activity}', [ProspectActivityController::class, 'destroy'])->middleware('permission:manage prospects')->name('team.prospects.activities.destroy');
    Route::get('/team/prospects/pipeline', [ProspectManagementController::class, 'pipeline'])->middleware('permission:manage prospects')->name('team.prospects.pipeline');
    Route::get('/team/prospects/follow-ups', [ProspectManagementController::class, 'followUps'])->middleware('permission:manage prospects')->name('team.prospects.follow-ups');
    Route::get('/team/prospects/appointments', [ProspectManagementController::class, 'appointments'])->middleware('permission:manage prospects')->name('team.prospects.appointments');
    Route::get('/team/prospects/access-manager', [ProspectManagementController::class, 'accessManager'])->middleware('permission:manage prospects')->name('team.prospects.access-manager');
    Route::get('/team/prospects/shared-with-me', [ProspectManagementController::class, 'sharedWithMe'])->middleware('permission:manage prospects')->name('team.prospects.shared-with-me');
    Route::get('/team/prospects/shared-by-me', [ProspectManagementController::class, 'sharedByMe'])->middleware('permission:manage prospects')->name('team.prospects.shared-by-me');
    Route::get('/team/prospects/analytics', [ProspectManagementController::class, 'analytics'])->middleware('permission:manage prospects')->name('team.prospects.analytics');
    Route::get('/team/prospects/ai-coach', [ProspectManagementController::class, 'aiCoach'])->middleware('permission:manage prospects')->name('team.prospects.ai-coach');
    Route::get('/team/prospects/import', [ProspectManagementController::class, 'import'])->middleware('permission:import prospects')->name('team.prospects.import');
    Route::get('/team/prospects/export', [ProspectManagementController::class, 'export'])->middleware('permission:export prospects')->name('team.prospects.export');
    Route::get('/team/prospects/{screen}', [ProspectManagementController::class, 'placeholder'])->middleware('permission:manage prospects')->name('team.prospects.screen');

    Route::prefix('team/fna')->name('team.fna.')->group(function (): void {
        Route::middleware('permission:manage fna records')->group(function (): void {
            Route::get('/', [FnaManagementController::class, 'dashboard'])->name('dashboard');
            Route::get('/records', [FnaManagementController::class, 'index'])->name('index');
            Route::get('/records/create', [FnaManagementController::class, 'create'])->name('create');
            Route::post('/records', [FnaManagementController::class, 'store'])->name('store');
            Route::get('/records/{fnaRecord}', [FnaManagementController::class, 'show'])->name('show');
            Route::get('/records/{fnaRecord}/edit', [FnaManagementController::class, 'edit'])->name('edit');
            Route::get('/records/{fnaRecord}/wizard', [FnaManagementController::class, 'wizard'])->name('wizard');
            Route::get('/dime-calculator', [FnaManagementController::class, 'dimeCalculator'])->name('dime');
            Route::get('/records/{fnaRecord}/export', [FnaManagementController::class, 'export'])
                ->middleware('permission:export fna records')
                ->name('export');
            Route::get('/records/{fnaRecord}/export/download', [FnaManagementController::class, 'exportDownload'])
                ->middleware('permission:export fna records')
                ->name('export.download');
        });

        Route::get('/cfm/review-queue', [FnaManagementController::class, 'cfmReviewQueue'])
            ->middleware('permission:review trainee fna records')
            ->name('cfm.review-queue');

        Route::get('/reports/agency', [FnaManagementController::class, 'agencyReports'])
            ->middleware('permission:view fna agency reports')
            ->name('reports.agency');
    });

    Route::get('/team/prospects/records/{prospect}/fna', [FnaManagementController::class, 'prospectFnas'])
        ->middleware('permission:manage prospects')
        ->name('team.prospects.records.fna');

    Route::view('/resources', 'resources.index')->name('resources.index');
    Route::get('/resources/documents', [ResourceDocumentsController::class, 'index'])->name('resources.documents');
    Route::get('/resources/documents/{portalResource}/preview', [ResourceDocumentsController::class, 'preview'])->name('resources.documents.preview');
    Route::get('/resources/documents/{portalResource}/view', [ResourceDocumentsController::class, 'view'])->name('resources.documents.view');
    Route::get('/resources/documents/{portalResource}/download', [ResourceDocumentsController::class, 'download'])->name('resources.documents.download');
    Route::post('/resources/documents/{portalResource}/favorite', [ResourceDocumentsController::class, 'toggleFavorite'])->name('resources.documents.favorite');
    Route::post('/resources/documents/update-seeder', [ResourceDocumentsController::class, 'updateSeeder'])->name('resources.documents.update-seeder');
    Route::get('/resources/forms/associate-participation-agreement', [AssociateParticipationAgreementController::class, 'show'])->name('resources.forms.associate-participation-agreement');
    Route::post('/resources/forms/associate-participation-agreement', [AssociateParticipationAgreementController::class, 'store'])->name('resources.forms.associate-participation-agreement.store');
    Route::get('/resources/forms/associate-participation-agreement/download', [AssociateParticipationAgreementController::class, 'downloadPdf'])->name('resources.forms.associate-participation-agreement.download');
    Route::view('/resources/videos', 'resources.videos')->name('resources.videos');
    Route::view('/resources/recorded-webinars', 'resources.recorded-webinars')->name('resources.recorded-webinars');
    Route::redirect('/resources/zoom-links', '/resources/links');
    Route::get('/resources/links', [ResourceLinksController::class, 'index'])->name('resources.links');
    Route::get('/events', [CalendarController::class, 'agenda'])->middleware('permission:view calendar')->name('events.index');
    Route::get('/calendar', [CalendarController::class, 'index'])->middleware('permission:view calendar')->name('calendar.index');
    Route::post('/calendar', [CalendarController::class, 'store'])->middleware('permission:create calendar events')->name('calendar.store');
    Route::get('/calendar/month', [CalendarController::class, 'month'])->middleware('permission:view calendar')->name('calendar.month');
    Route::get('/calendar/week', [CalendarController::class, 'week'])->middleware('permission:view calendar')->name('calendar.week');
    Route::get('/calendar/day', [CalendarController::class, 'day'])->middleware('permission:view calendar')->name('calendar.day');
    Route::get('/calendar/agenda', [CalendarController::class, 'agenda'])->middleware('permission:view calendar')->name('calendar.agenda');
    Route::get('/calendar/events/{event}', [CalendarController::class, 'show'])->middleware('permission:view calendar')->name('calendar.events.show');
    Route::post('/calendar/categories', [CalendarController::class, 'storeCategory'])->middleware('permission:view calendar')->name('calendar.categories.store');
    Route::patch('/calendar/categories/{category}', [CalendarController::class, 'updateCategory'])->middleware('permission:view calendar')->name('calendar.categories.update');
    Route::delete('/calendar/categories/{category}', [CalendarController::class, 'destroyCategory'])->middleware('permission:view calendar')->name('calendar.categories.destroy');
    Route::get('/calendar/settings', [CalendarController::class, 'settings'])->middleware('permission:view calendar')->name('calendar.settings');
    Route::get('/calendar/export', [CalendarController::class, 'export'])->middleware('permission:view calendar')->name('calendar.export');
    Route::get('/bookings', [BookingController::class, 'dashboard'])->middleware('permission:view booking dashboard')->name('bookings.dashboard');
    Route::get('/bookings/availability', [BookingController::class, 'availability'])->middleware('permission:manage own availability')->name('bookings.availability');
    Route::get('/bookings/event-types', [BookingController::class, 'eventTypes'])->middleware('permission:manage own booking event types')->name('bookings.event-types');
    Route::get('/bookings/links', [BookingController::class, 'links'])->middleware('permission:create booking links')->name('bookings.links');
    Route::get('/bookings/requests', [BookingController::class, 'requests'])->middleware('permission:approve booking requests')->name('bookings.requests');
    Route::get('/bookings/my', [BookingController::class, 'myBookings'])->middleware('permission:view own bookings')->name('bookings.my');
    Route::get('/bookings/calendar', [BookingController::class, 'calendar'])->middleware('permission:view own bookings')->name('bookings.calendar');
    Route::get('/bookings/settings', [BookingController::class, 'settings'])->middleware('permission:manage booking settings')->name('bookings.settings');
    Route::view('/recognition', 'recognition.index')->name('recognition.index');
    Route::view('/announcements', 'announcements.index')->name('announcements.index');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.mark-read');

    Route::prefix('support')->name('support.')->group(function (): void {
        Route::get('/', [SupportController::class, 'index'])
            ->middleware('permission:submit support ticket')
            ->name('index');
        Route::get('/documentation/{guide}', [SupportController::class, 'documentation'])
            ->middleware('permission:submit support ticket')
            ->name('documentation');
        Route::get('/tickets/{ticket}', function (\App\Models\SupportTicket $ticket) {
            abort_unless(auth()->user()?->can('view', $ticket), 403);

            return view('support.show', ['ticket' => $ticket]);
        })
            ->middleware('permission:view own support tickets')
            ->name('show');
    });

    Route::prefix('admin/support')
        ->name('admin.support.')
        ->middleware('permission:view all support tickets')
        ->group(function (): void {
            Route::view('/', 'admin.support.index')->name('index');
            Route::view('/wishlist', 'admin.support.wishlist')
                ->middleware('permission:manage enhancement wishlist')
                ->name('wishlist');
        });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/licenses', [ProfileController::class, 'updateInsuranceLicenses'])->name('profile.licenses.update');
    Route::patch('/profile/invite-link', [ProfileController::class, 'updateInviteLink'])->name('profile.invite-link.update');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo.update');
    Route::delete('/profile/photo', [ProfileController::class, 'destroyPhoto'])->name('profile.photo.destroy');
    Route::post('/profile/invitations', [ProfileController::class, 'createInvitation'])->name('profile.invitations.store');
    Route::post('/profile/invitations/{invitation}/send', [ProfileController::class, 'sendInvitationEmail'])->name('profile.invitations.send');
    Route::delete('/profile/invitations/{invitation}', [ProfileController::class, 'destroyInvitation'])->name('profile.invitations.destroy');

    Route::prefix('admin')
        ->name('admin.')
        ->middleware('role:super-admin|admin|agency-owner|team-leader|certified-field-mentor|trainer')
        ->group(function () {
            Route::get('/', AdminDashboardController::class)->name('index');
            Route::view('/settings', 'admin.settings')->middleware('role:super-admin|admin|agency-owner')->name('settings');
            Route::middleware('role:super-admin|admin|agency-owner')->group(function () {
                Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
                Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create');
                Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
                Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->withTrashed()->name('users.edit');
                Route::patch('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
                Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
                Route::patch('/users/{user}/restore', [UserManagementController::class, 'restore'])->name('users.restore');
            });
            Route::middleware('role:super-admin|admin|agency-owner|team-leader|certified-field-mentor|trainer')->prefix('management')->name('management.')->group(function () {
                Route::get('/', [AdminManagementController::class, 'index'])->name('index');
                Route::get('/{resource}', [AdminManagementController::class, 'resourceIndex'])->name('resource.index');
                Route::get('/{resource}/create', [AdminManagementController::class, 'create'])->name('create');
                Route::post('/{resource}', [AdminManagementController::class, 'store'])->name('store');
                Route::post('/{resource}/update-seeder', [AdminManagementController::class, 'updateSeeder'])->name('update-seeder');
                Route::get('/{resource}/{record}', [AdminManagementController::class, 'show'])->name('show');
                Route::get('/{resource}/{record}/edit', [AdminManagementController::class, 'edit'])->name('edit');
                Route::patch('/{resource}/{record}', [AdminManagementController::class, 'update'])->name('update');
                Route::patch('/{resource}/{record}/status', [AdminManagementController::class, 'toggleStatus'])->name('status');
                Route::delete('/{resource}/{record}', [AdminManagementController::class, 'destroy'])->name('destroy');
                Route::patch('/{resource}/{record}/restore', [AdminManagementController::class, 'restore'])->name('restore');
                Route::get('/resources/{record}/view-pdf', [AdminManagementController::class, 'viewResourcePdf'])->name('resources.view-pdf');
                Route::post('/resources/{record}/favorite', [AdminManagementController::class, 'toggleResourceFavorite'])->name('resources.favorite');
                Route::post('/resources/{record}/generate-pdf', [AdminManagementController::class, 'generateResourcePdf'])->name('resources.generate-pdf');
            });
            Route::view('/roles', 'admin.roles.index')->middleware('permission:manage roles')->name('roles.index');
            Route::view('/ranks', 'admin.ranks.index')->middleware('role:super-admin|admin|agency-owner')->name('ranks.index');
            Route::view('/checklists', 'admin.checklists.index')->middleware('role:super-admin|admin|agency-owner|team-leader|certified-field-mentor|trainer')->name('checklists.index');
            Route::get('/training', [\App\Http\Controllers\Admin\AdminTrainingController::class, 'index'])->middleware('permission:manage training')->name('training.index');
            Route::get('/training/courses', [\App\Http\Controllers\Admin\AdminTrainingController::class, 'courses'])->middleware('permission:manage training')->name('training.courses.index');
            Route::get('/training/courses/{module}', [\App\Http\Controllers\Admin\AdminTrainingController::class, 'course'])->middleware('permission:manage training')->name('training.courses.show');
            Route::get('/training/paths', [\App\Http\Controllers\Admin\AdminTrainingController::class, 'paths'])->middleware('permission:manage training')->name('training.paths.index');
            Route::get('/training/paths/{path}', [\App\Http\Controllers\Admin\AdminTrainingController::class, 'path'])->middleware('permission:manage training')->name('training.paths.show');
            Route::view('/cfm-certification', 'admin.cfm.index')->middleware('permission:manage CFM certification')->name('cfm.index');
        });
});

require __DIR__.'/auth.php';
