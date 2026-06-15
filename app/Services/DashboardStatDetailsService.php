<?php

namespace App\Services;

use App\Models\User;
use App\Support\MemberDisplayName;
use Illuminate\Support\Collection;

class DashboardStatDetailsService
{
    public const TYPES = ['profile', 'onboarding', 'credentials', 'apprenticeship', 'training'];

    public function __construct(
        private readonly DownlineHierarchyService $hierarchy,
        private readonly DashboardStatsService $stats,
        private readonly ProfileCompletionService $profileCompletion,
    ) {}

    public function isValidType(string $type): bool
    {
        return in_array($type, self::TYPES, true);
    }

    public function scopeLabel(User $viewer): string
    {
        if ($viewer->hasPermissionTo('view full downline')) {
            return 'full_downline';
        }

        if ($viewer->hasPermissionTo('view direct downline')) {
            return 'direct_downline';
        }

        return 'self';
    }

    /**
     * @return array{
     *     type: string,
     *     title: string,
     *     scope: string,
     *     members: list<array{id: int, name: string, email: string, rank: string, percent: int, status: string}>
     * }
     */
    public function membersFor(User $viewer, string $type): array
    {
        $members = $this->scopedMembers($viewer)
            ->loadMissing(['profile', 'rank'])
            ->map(fn (User $member): ?array => $this->memberRow($member, $type))
            ->filter()
            ->sortBy([
                ['percent', 'asc'],
                ['name', 'asc'],
            ])
            ->values()
            ->all();

        return [
            'type' => $type,
            'title' => $this->titleFor($type),
            'scope' => $this->scopeLabel($viewer),
            'members' => $members,
        ];
    }

    /**
     * @return Collection<int, User>
     */
    private function scopedMembers(User $viewer): Collection
    {
        return $this->hierarchy
            ->dashboardMembersQuery($viewer)
            ->orderBy('users.name')
            ->get();
    }

    /**
     * @return array{id: int, name: string, email: string, rank: string, percent: int, status: string}|null
     */
    private function memberRow(User $member, string $type): ?array
    {
        if ($type === 'credentials' && $this->memberHasLicense($member)) {
            return null;
        }

        if ($type === 'training' && ! $this->stats->hasStartedTraining($member)) {
            return null;
        }

        $percent = $this->percentFor($member, $type);

        if ($percent >= 100) {
            return null;
        }

        return [
            'id' => $member->id,
            'name' => MemberDisplayName::for($member),
            'email' => $member->email,
            'rank' => $member->rank?->code ?? 'FA',
            'percent' => $percent,
            'status' => $this->statusLabel($percent),
        ];
    }

    private function percentFor(User $member, string $type): int
    {
        return match ($type) {
            'profile' => $this->profileCompletion->percent($member),
            'onboarding' => $this->stats->onboardingPercent($member),
            'credentials' => $this->stats->licensingPercent($member),
            'apprenticeship' => $this->stats->apprenticeshipPercent($member),
            'training' => $this->stats->trainingPercent($member),
            default => 0,
        };
    }

    private function statusLabel(int $percent): string
    {
        if ($percent <= 0) {
            return 'Not started';
        }

        return 'In progress';
    }

    private function memberHasLicense(User $member): bool
    {
        $member->loadMissing('profile');

        return filled($member->profile?->license_number);
    }

    private function titleFor(string $type): string
    {
        return match ($type) {
            'profile' => 'Incomplete Profiles',
            'onboarding' => 'Members In Onboarding',
            'credentials' => 'Unlicensed Members',
            'apprenticeship' => 'Field Apprenticeship In Progress',
            'training' => 'CFM Training In Progress',
            default => 'Members',
        };
    }
}
