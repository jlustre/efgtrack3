<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateCfmPortalProfileRequest;
use App\Services\CfmPortalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CfmPortalController extends Controller
{
    public function __construct(private readonly CfmPortalService $cfmPortal) {}

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
}
