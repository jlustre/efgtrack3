<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateCfmPortalCalendarSharingRequest;
use App\Http\Requests\UpdateCfmPortalProfileRequest;
use App\Services\CalendarShareService;
use App\Services\CfmAssignmentWorkflowService;
use App\Services\CfmPortalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CfmPortalController extends Controller
{
    public function __construct(
        private readonly CfmPortalService $cfmPortal,
        private readonly CalendarShareService $calendarShare,
        private readonly CfmAssignmentWorkflowService $assignmentWorkflow,
    ) {}

    public function index(Request $request): View
    {
        $viewer = $request->user()->loadMissing(['profile', 'rank', 'team']);
        $cfmUserId = $request->integer('cfm') ?: null;

        $payload = $this->cfmPortal->payloadFor($viewer, $cfmUserId);

        return view('cfm-portal.index', [
            'user' => $viewer,
            'todayLabel' => now()->format('l, F j, Y'),
            'portal' => $payload,
            'openEditProfileModal' => (bool) session('open_edit_profile_modal', false),
        ]);
    }

    public function updateCalendarSharing(UpdateCfmPortalCalendarSharingRequest $request): RedirectResponse
    {
        $this->calendarShare->updateSharingSettings($request->user(), $request->validated());

        return redirect()
            ->route('cfm.portal')
            ->with('profile_feedback', [
                'type' => 'success',
                'message' => 'Calendar sharing preferences were updated.',
            ]);
    }

    public function updateProfile(UpdateCfmPortalProfileRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['licensed_jurisdictions'] = $request->normalizedLicensedJurisdictions();

        $this->cfmPortal->updateProfile($request->user(), $validated);

        return redirect()
            ->route('cfm.portal')
            ->with('profile_feedback', [
                'type' => 'success',
                'message' => 'Your CFM profile was updated successfully.',
            ]);
    }

    public function confirmAssignment(Request $request, \App\Models\MentorAssignment $assignment): RedirectResponse
    {
        $this->assignmentWorkflow->confirmAssignment($assignment, $request->user());

        $assignment->loadMissing('apprentice');

        return redirect()
            ->route('cfm.portal')
            ->with('profile_feedback', [
                'type' => 'success',
                'message' => $assignment->apprentice->name.' is now your active trainee. Send your first welcome email from the trainee list.',
            ]);
    }

    public function sendFirstContact(Request $request, \App\Models\MentorAssignment $assignment): RedirectResponse
    {
        $this->assignmentWorkflow->sendFirstContactEmail($assignment, $request->user());

        $assignment->loadMissing('apprentice');

        return redirect()
            ->route('cfm.portal')
            ->with('profile_feedback', [
                'type' => 'success',
                'message' => 'Your first welcome email was sent to '.$assignment->apprentice->name.'.',
            ]);
    }
}
