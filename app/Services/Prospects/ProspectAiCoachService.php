<?php

namespace App\Services\Prospects;

use App\Models\Prospect;
use App\Models\ProspectFollowUp;
use App\Models\User;
use Illuminate\Support\Collection;

class ProspectAiCoachService
{
    public function __construct(private ProspectFollowUpEngine $followUpEngine) {}

    /**
     * @return array{
     *     recommendations: list<array<string, mixed>>,
     *     stalled_prospects: list<array<string, mixed>>,
     *     high_value_opportunities: list<array<string, mixed>>,
     * }
     */
    public function recommendationsFor(User $user): array
    {
        $prospects = Prospect::query()
            ->with(['stage'])
            ->where('owner_id', $user->id)
            ->where('status', 'active')
            ->where('is_archived', false)
            ->get();

        $recommendations = collect();

        foreach ($prospects as $prospect) {
            $recommendations = $recommendations->merge(
                $this->buildRecommendationsForProspect($prospect, $user)
            );
        }

        $sorted = $this->sortByPriority($recommendations);

        return [
            'recommendations' => $sorted->values()->all(),
            'stalled_prospects' => $this->stalledProspects($prospects)->values()->all(),
            'high_value_opportunities' => $this->highValueOpportunities($prospects)->values()->all(),
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function recommendationsForProspect(Prospect $prospect, ?User $user = null): array
    {
        $prospect->loadMissing('stage');
        $user ??= auth()->user();

        return $this->sortByPriority(
            $this->buildRecommendationsForProspect($prospect, $user)
        )->values()->all();
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function buildRecommendationsForProspect(Prospect $prospect, ?User $user): Collection
    {
        if ($prospect->status !== 'active' || $prospect->is_archived) {
            return collect();
        }

        $rules = config('prospects.ai_coach_rules', []);
        $items = collect();

        foreach ($rules as $ruleKey => $rule) {
            if (! is_array($rule)) {
                continue;
            }

            if ($ruleKey === 'overdue_followup') {
                if ($this->matchesOverdueFollowUp($prospect, $user)) {
                    $items->push($this->formatRecommendation($prospect, $ruleKey, $rule));
                }

                continue;
            }

            $engineRule = config("prospects.follow_up_engine_rules.{$ruleKey}", $rule);

            if ($this->followUpEngine->evaluateRule($ruleKey, is_array($engineRule) ? $engineRule : $rule, $prospect)) {
                $items->push($this->formatRecommendation($prospect, $ruleKey, $rule));
            }
        }

        return $items;
    }

    /**
     * @param  array<string, mixed>  $rule
     * @return array<string, mixed>
     */
    private function formatRecommendation(Prospect $prospect, string $ruleKey, array $rule): array
    {
        $offsetDays = (int) ($rule['suggested_due_offset_days'] ?? 0);
        $suggestedDue = $offsetDays === 0 && ($rule['suggested_action'] ?? '') === 'act_today'
            ? now()->endOfDay()
            : now()->addDays($offsetDays);

        return [
            'rule_key' => $ruleKey,
            'prospect_id' => $prospect->id,
            'prospect_name' => $prospect->displayName(),
            'priority' => $this->normalizePriority($rule['priority'] ?? 'medium'),
            'message' => $rule['message'] ?? ($rule['title'] ?? 'Follow up recommended'),
            'suggested_action' => $rule['suggested_action'] ?? 'log_call',
            'suggested_due' => $suggestedDue->toIso8601String(),
        ];
    }

    private function normalizePriority(string $priority): string
    {
        return match ($priority) {
            'urgent', 'high' => 'high',
            'low' => 'low',
            default => 'medium',
        };
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $recommendations
     * @return Collection<int, array<string, mixed>>
     */
    private function sortByPriority(Collection $recommendations): Collection
    {
        $order = ['high' => 1, 'medium' => 2, 'low' => 3];

        return $recommendations->sortBy(fn (array $item): int => $order[$item['priority']] ?? 4);
    }

    private function matchesOverdueFollowUp(Prospect $prospect, ?User $user): bool
    {
        if ($prospect->next_follow_up_at?->isPast()) {
            return true;
        }

        if (! $user) {
            return false;
        }

        return ProspectFollowUp::query()
            ->where('prospect_id', $prospect->id)
            ->where('assigned_user_id', $user->id)
            ->whereIn('status', ['pending', 'overdue'])
            ->where('due_at', '<', now())
            ->exists();
    }

    /**
     * @param  Collection<int, Prospect>  $prospects
     * @return Collection<int, array<string, mixed>>
     */
    private function stalledProspects(Collection $prospects): Collection
    {
        $stalledSlugs = ['application-submitted', 'registration-link-sent'];

        return $prospects
            ->filter(function (Prospect $prospect) use ($stalledSlugs): bool {
                if (! in_array($prospect->stage?->slug, $stalledSlugs, true)) {
                    return false;
                }

                $ruleKey = $prospect->stage?->slug === 'application-submitted'
                    ? 'application_stalled'
                    : 'registration_incomplete';

                $engineRule = config("prospects.follow_up_engine_rules.{$ruleKey}", []);

                return is_array($engineRule)
                    && $this->followUpEngine->evaluateRule($ruleKey, $engineRule, $prospect);
            })
            ->map(fn (Prospect $prospect): array => [
                'prospect_id' => $prospect->id,
                'prospect_name' => $prospect->displayName(),
                'stage' => $prospect->stage?->name,
                'stage_slug' => $prospect->stage?->slug,
                'last_activity_at' => $prospect->last_activity_at?->toIso8601String(),
            ]);
    }

    /**
     * @param  Collection<int, Prospect>  $prospects
     * @return Collection<int, array<string, mixed>>
     */
    private function highValueOpportunities(Collection $prospects): Collection
    {
        return $prospects
            ->filter(function (Prospect $prospect): bool {
                if ($prospect->interest_level !== 'hot') {
                    return false;
                }

                $recentActivity = ($prospect->last_activity_at ?? $prospect->last_contacted_at)?->gte(now()->subDays(7));
                $highScore = (int) ($prospect->interest_score ?? 0) >= 7;

                return $recentActivity || $highScore;
            })
            ->map(fn (Prospect $prospect): array => [
                'prospect_id' => $prospect->id,
                'prospect_name' => $prospect->displayName(),
                'interest_level' => $prospect->interest_level,
                'interest_score' => $prospect->interest_score,
                'stage' => $prospect->stage?->name,
                'last_activity_at' => $prospect->last_activity_at?->toIso8601String(),
            ]);
    }
}
