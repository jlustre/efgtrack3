<?php



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

