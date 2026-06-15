<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminManagementController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CfmManagementController;
use App\Http\Controllers\CfmPortalController;
use App\Http\Controllers\DownlineController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProspectActivityController;
use App\Http\Controllers\ProspectManagementController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TrackerChecklistController;
use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Artisan;

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

Route::middleware(['auth', 'verified', 'active'])->group(function () {
    Route::view('/dashboard', 'dashboard')
        ->middleware('permission:view dashboard')
        ->name('dashboard');

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
    Route::view('/training', 'training.index')->name('training.index');
    Route::view('/assessments', 'assessments.index')->name('assessments.index');
    Route::view('/rank-advancement', 'rank-advancement.index')->name('rank-advancement.index');
    Route::get('/team', [DownlineController::class, 'index'])->middleware('permission:view own team')->name('team.index');
    Route::get('/team/tree', [DownlineController::class, 'tree'])->middleware('permission:view team tree')->name('team.tree');
    Route::get('/team/org-chart', [DownlineController::class, 'orgChart'])->middleware('permission:view org chart')->name('team.org-chart');
    Route::get('/team/table', [DownlineController::class, 'table'])->middleware('permission:view team table')->name('team.table');
    Route::get('/team/hierarchy', [DownlineController::class, 'hierarchyTable'])->middleware('permission:view team tree')->name('team.hierarchy');
    Route::get('/team/member/{user}', [DownlineController::class, 'member'])->middleware('permission:view own team')->name('team.member');
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
    Route::post('/team/prospects', [ProspectManagementController::class, 'store'])->middleware('permission:manage prospects')->name('team.prospects.store');
    Route::get('/team/prospects/records/{prospect}', [ProspectManagementController::class, 'show'])->middleware('permission:manage prospects')->name('team.prospects.records.show');
    Route::get('/team/prospects/records/{prospect}/edit', [ProspectManagementController::class, 'edit'])->middleware('permission:manage prospects')->name('team.prospects.records.edit');
    Route::patch('/team/prospects/records/{prospect}', [ProspectManagementController::class, 'update'])->middleware('permission:manage prospects')->name('team.prospects.records.update');
    Route::patch('/team/prospects/records/{prospect}/archive', [ProspectManagementController::class, 'archive'])->middleware('permission:manage prospects')->name('team.prospects.records.archive');
    Route::delete('/team/prospects/records/{prospect}', [ProspectManagementController::class, 'destroy'])->middleware('permission:manage prospects')->name('team.prospects.records.destroy');
    Route::get('/team/prospects/records/{prospect}/activities', [ProspectActivityController::class, 'index'])->middleware('permission:manage prospects')->name('team.prospects.activities.index');
    Route::post('/team/prospects/records/{prospect}/activities', [ProspectActivityController::class, 'store'])->middleware('permission:manage prospects')->name('team.prospects.activities.store');
    Route::patch('/team/prospects/records/{prospect}/activities/{activity}', [ProspectActivityController::class, 'update'])->middleware('permission:manage prospects')->name('team.prospects.activities.update');
    Route::delete('/team/prospects/records/{prospect}/activities/{activity}', [ProspectActivityController::class, 'destroy'])->middleware('permission:manage prospects')->name('team.prospects.activities.destroy');
    Route::get('/team/prospects/{screen}', [ProspectManagementController::class, 'placeholder'])->middleware('permission:manage prospects')->name('team.prospects.screen');
    Route::view('/resources', 'resources.index')->name('resources.index');
    Route::view('/resources/documents', 'resources.documents')->name('resources.documents');
    Route::view('/resources/videos', 'resources.videos')->name('resources.videos');
    Route::view('/resources/recorded-webinars', 'resources.recorded-webinars')->name('resources.recorded-webinars');
    Route::view('/resources/zoom-links', 'resources.zoom-links')->name('resources.zoom-links');
    Route::get('/events', [CalendarController::class, 'agenda'])->middleware('permission:view calendar')->name('events.index');
    Route::get('/calendar', [CalendarController::class, 'index'])->middleware('permission:view calendar')->name('calendar.index');
    Route::post('/calendar', [CalendarController::class, 'store'])->middleware('permission:create calendar events')->name('calendar.store');
    Route::get('/calendar/month', [CalendarController::class, 'month'])->middleware('permission:view calendar')->name('calendar.month');
    Route::get('/calendar/week', [CalendarController::class, 'week'])->middleware('permission:view calendar')->name('calendar.week');
    Route::get('/calendar/day', [CalendarController::class, 'day'])->middleware('permission:view calendar')->name('calendar.day');
    Route::get('/calendar/agenda', [CalendarController::class, 'agenda'])->middleware('permission:view calendar')->name('calendar.agenda');
    Route::get('/calendar/events/{event}', [CalendarController::class, 'show'])->middleware('permission:view calendar')->name('calendar.events.show');
    Route::patch('/calendar/categories/{category}', [CalendarController::class, 'updateCategory'])->middleware('permission:manage organization calendar')->name('calendar.categories.update');
    Route::delete('/calendar/categories/{category}', [CalendarController::class, 'destroyCategory'])->middleware('permission:manage organization calendar')->name('calendar.categories.destroy');
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

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo.update');
    Route::delete('/profile/photo', [ProfileController::class, 'destroyPhoto'])->name('profile.photo.destroy');
    Route::patch('/profile/invite-link', [ProfileController::class, 'updateInviteLink'])->name('profile.invite-link.update');
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
            });
            Route::view('/roles', 'admin.roles.index')->middleware('permission:manage roles')->name('roles.index');
            Route::view('/ranks', 'admin.ranks.index')->middleware('role:super-admin|admin|agency-owner')->name('ranks.index');
            Route::view('/checklists', 'admin.checklists.index')->middleware('role:super-admin|admin|agency-owner|team-leader|certified-field-mentor|trainer')->name('checklists.index');
            Route::view('/training', 'admin.training.index')->middleware('permission:manage training')->name('training.index');
            Route::view('/cfm-certification', 'admin.cfm.index')->middleware('permission:manage CFM certification')->name('cfm.index');
        });
});

require __DIR__.'/auth.php';
