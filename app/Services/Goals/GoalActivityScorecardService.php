<?php

namespace App\Services\Goals;

use App\Models\GoalActivityTarget;
use App\Models\User;
use Carbon\Carbon;

class GoalActivityScorecardService
{
    public function __construct(
        private readonly GoalMetricResolver $metricResolver,
    ) {}

    /**
     * @return array{
     *     period_type: string,
     *     period_label: string,
     *     activities: list<array{key: string, label: string, target: float, actual: float, percent: int}>,
     *     overall_score: int,
     * }
     */
    public function scorecardFor(User $user, string $periodType = 'weekly'): array
    {
        [$start, $end, $label] = $this->periodBounds($periodType);
        $definitions = config('goals-planning.activity_scorecard', []);
        $activities = [];

        foreach ($definitions as $key => $definition) {
            $metricKey = $definition['metric_key'] ?? $key;
            $actual = $this->metricResolver->resolve($user, $metricKey, $start, $end);
            $target = (float) GoalActivityTarget::query()
                ->whereHas('goal', fn ($q) => $q->where('user_id', $user->id)->where('status', 'active'))
                ->where('activity_key', $key)
                ->where('period_type', $periodType === 'annual' ? 'monthly' : $periodType)
                ->sum('target_value');

            if ($target <= 0) {
                $target = $this->defaultTarget($key, $periodType);
            }

            $percent = $target > 0 ? (int) min(100, round(($actual / $target) * 100)) : 0;

            $activities[] = [
                'key' => $key,
                'label' => $definition['label'] ?? $key,
                'target' => round($target, 1),
                'actual' => round($actual, 1),
                'percent' => $percent,
            ];
        }

        $overall = $activities === [] ? 0 : (int) round(collect($activities)->avg('percent'));

        return [
            'period_type' => $periodType,
            'period_label' => $label,
            'activities' => $activities,
            'overall_score' => $overall,
        ];
    }

    /**
     * @return array{0: Carbon, 1: Carbon, 2: string}
     */
    private function periodBounds(string $periodType): array
    {
        return match ($periodType) {
            'daily' => [now()->startOfDay(), now()->endOfDay(), now()->format('M j, Y')],
            'monthly' => [now()->startOfMonth(), now()->endOfMonth(), now()->format('F Y')],
            'quarterly' => [now()->startOfQuarter(), now()->endOfQuarter(), 'Q'.now()->quarter.' '.now()->year],
            'annual' => [now()->startOfYear(), now()->endOfYear(), (string) now()->year],
            default => [now()->startOfWeek(), now()->endOfWeek(), 'Week of '.now()->startOfWeek()->format('M j')],
        };
    }

    private function defaultTarget(string $key, string $periodType): float
    {
        $weeklyDefaults = [
            'contacts' => 25,
            'appointments' => 5,
            'presentations' => 3,
            'fna_completed' => 2,
            'applications' => 1,
            'new_prospects' => 10,
            'followups_completed' => 15,
            'invitations_sent' => 10,
            'recruits' => 0.5,
        ];

        $base = (float) ($weeklyDefaults[$key] ?? 5);

        return match ($periodType) {
            'daily' => round($base / 5, 1),
            'monthly' => round($base * 4.33, 1),
            'quarterly' => round($base * 13, 1),
            'annual' => round($base * 48, 1),
            default => $base,
        };
    }
}
