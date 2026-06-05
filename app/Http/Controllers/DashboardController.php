<?php

namespace App\Http\Controllers;

use App\Services\DashboardStatsService;
use App\Services\ProfileCompletionService;
use App\Support\LocationOptions;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(
        Request $request,
        ProfileCompletionService $profileCompletion,
        DashboardStatsService $dashboardStats,
    ): View {
        $user = $request->user()->loadMissing([
            'profile.countryRecord',
            'profile.stateProvince',
            'profile.timezoneRecord',
        ]);
        $promptOnLogin = (bool) session()->pull('show_profile_completion_modal', false);
        $profileSnapshot = $profileCompletion->snapshot($user);

        return view('dashboard', [
            'user' => $user,
            'profileCompletion' => $profileSnapshot,
            'statCards' => $dashboardStats->statCards($user, $profileSnapshot),
            'locationOptions' => LocationOptions::forPortal(),
            'forceProfileCompletionModal' => $promptOnLogin,
        ]);
    }
}
