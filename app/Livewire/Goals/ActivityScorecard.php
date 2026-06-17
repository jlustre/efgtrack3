<?php

namespace App\Livewire\Goals;

use App\Models\Goal;
use App\Services\Goals\GoalActivityScorecardService;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ActivityScorecard extends Component
{
    public string $periodType = 'weekly';

    public function mount(): void
    {
        $this->authorize('viewAny', Goal::class);
    }

    public function render(GoalActivityScorecardService $scorecard): View
    {
        return view('livewire.goals.activity-scorecard', [
            'scorecard' => $scorecard->scorecardFor(auth()->user(), $this->periodType),
            'periods' => [
                'daily' => 'Daily',
                'weekly' => 'Weekly',
                'monthly' => 'Monthly',
                'quarterly' => 'Quarterly',
                'annual' => 'Annual',
            ],
        ]);
    }
}
