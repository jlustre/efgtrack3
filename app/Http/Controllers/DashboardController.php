<?php

namespace App\Http\Controllers;

use App\Services\DashboardOverviewService;
use App\Services\DashboardStatDetailsService;
use App\Services\DashboardStatsService;
use App\Services\ProfileCompletionService;
use App\Support\LocationOptions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardStatsService $stats,
        private readonly DashboardStatDetailsService $statDetails,
        private readonly DashboardOverviewService $overview,
    ) {}

    public function index(Request $request, ProfileCompletionService $profileCompletion): View
    {
        $user = $request->user();
        $user->loadMissing([
            'profile',
            'rank',
            'team',
            'sponsor',
        ]);

        return view('dashboard', [
            'user' => $user,
            'statCards' => $this->stats->statCards($user),
            'overview' => $this->overview->forUser($user),
            'profileCompletion' => $profileCompletion->snapshot($user),
            'locationOptions' => LocationOptions::forPortal(),
            'forceProfileCompletionModal' => (bool) $request->session()->pull('show_profile_completion_modal', false),
        ]);
    }

    public function statDetails(Request $request, string $type): JsonResponse
    {
        abort_unless($this->statDetails->isValidType($type), 404);

        return response()->json(
            $this->statDetails->membersFor($request->user(), $type)
        );
    }
}
