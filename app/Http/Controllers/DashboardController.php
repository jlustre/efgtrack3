<?php

<<<<<<< HEAD


namespace App\Http\Controllers;



use App\Services\DashboardOverviewService;
use App\Services\DashboardStatDetailsService;
use App\Services\DashboardStatsService;

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



    public function index(Request $request): View

    {

        $user = $request->user();

        return view('dashboard', [
            'statCards' => $this->stats->statCards($user),
            'overview' => $this->overview->forUser($user),
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

=======
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
>>>>>>> 2ae99211b388cde4b56062c1cfbbc9ca81c523b0
