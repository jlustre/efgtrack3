<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\CfmAdvancementGuideline;
use App\Models\CfmMentorProfile;
use App\Models\CfmRankTier;
use App\Models\CfmRecommendationSuggestion;
use App\Models\MentorAssignment;
use App\Models\TeamVisibilityPermission;
use App\Models\User;
use App\Models\UserTask;
use App\Support\LocationOptions;
use App\Support\MemberDisplayName;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CfmManagementService
{
    private const WEEKLY_SLOT_CAPACITY = 12;

    public function __construct(private readonly DownlineHierarchyService $hierarchy) {}

    public function rankStructureFor(): array
    {
        return [
            'tiers' => CfmRankTier::query()->active()->get()->values()->all(),
            'guideline' => CfmAdvancementGuideline::query()->active()->value('body'),
        ];
    }

    public function payloadFor(User $viewer): array
    {
        $cfms = $this->accessibleCfms($viewer);
        $fapQueue = $this->fapQueueFor($viewer);

        return [
            'stats' => $this->statsFrom($cfms, $viewer),
            'cfms' => $cfms->values()->all(),
            'assignableAssociates' => collect($fapQueue)->map(fn (array $row) => [
                'id' => $row['id'],
                'name' => $row['name'],
                'rank' => $row['rank'],
                'country' => $row['country'] ?? '',
                'province' => $row['province'] ?? '',
                'jurisdictionKey' => $row['jurisdictionKey'] ?? '',
                'locationLabel' => filled($row['province'] ?? null) && filled($row['country'] ?? null)
                    ? LocationOptions::formatJurisdictionLabel($row['country'], $row['province'])
                    : '',
            ])->values()->all(),
            'fapQueue' => $fapQueue,
            'cfmCandidates' => $this->cfmCandidatesFor($viewer),
            'aiSuggestions' => CfmRecommendationSuggestion::query()
                ->active()
                ->with('cfm:id,name')
                ->get()
                ->map(fn (CfmRecommendationSuggestion $row) => [
                    'type' => $row->recommendation_type,
                    'label' => $row->label,
                    'cfmName' => $row->cfm?->name ?? $row->cfm_name,
                    'fitScore' => $row->fit_score,
                    'statusLabel' => $row->status_label,
                    'detail' => $row->detail,
                ])
                ->values()
                ->all(),
            'filterOptions' => [
                'countries' => $cfms->pluck('country')->filter(fn ($v) => $v !== '—')->unique()->sort()->values()->all(),
                'ranks' => $cfms->pluck('rank')->filter(fn ($v) => $v !== '—')->unique()->sort()->values()->all(),
                'timezones' => $cfms->pluck('timezone')->filter(fn ($v) => $v !== '—')->unique()->sort()->values()->all(),
            ],
            'assignUrl' => route('team.cfms.assign'),
            'addCfmUrl' => route('team.cfms.store'),
            'locationOptions' => LocationOptions::forPortal(),
        ];
    }

    public function updateLicensedJurisdictions(User $viewer, User $cfm, array $licensedJurisdictions): array
    {
        if (! $cfm->hasRole('certified-field-mentor')) {
            throw ValidationException::withMessages([
                'licensed_jurisdictions' => 'This member is not a Certified Field Mentor.',
            ]);
        }

        if (! $this->canManageCfm($viewer, $cfm)) {
            abort(403);
        }

        $keys = LocationOptions::normalizeLicensedJurisdictionKeys($licensedJurisdictions);

        CfmMentorProfile::updateOrCreate(
            ['user_id' => $cfm->id],
            [
                'certification_status' => 'certified',
                'hierarchy_access' => 'my_hierarchy',
                'max_apprentices' => 6,
                'licensed_jurisdictions' => $keys,
                'last_mentor_activity_at' => now(),
            ]
        );

        return [
            'message' => 'Licensed jurisdictions updated for '.$cfm->name.'.',
            'licensedJurisdictions' => $keys,
            'licensedJurisdictionsLabel' => $this->licensedJurisdictionsLabel($keys),
        ];
    }

    private function canManageCfm(User $viewer, User $cfm): bool
    {
        if (! $viewer->canAccessCfmManagement()) {
            return false;
        }

        if ($viewer->hasAnyRole(['super-admin', 'admin'])) {
            return true;
        }

        $myHierarchyIds = $this->hierarchy->descendantsQuery($viewer)->pluck('users.id');
        if ($myHierarchyIds->contains($cfm->id)) {
            return true;
        }

        return TeamVisibilityPermission::query()
            ->where('viewer_id', $viewer->id)
            ->where('visible_user_id', $cfm->id)
            ->exists();
    }

    public function profileFor(User $viewer, User $cfm): array
    {
        $cfm->loadMissing(['profile', 'rank', 'team', 'sponsor', 'cfmMentorProfile']);

        $myHierarchyIds = $this->hierarchy->descendantsQuery($viewer)->pluck('users.id');
        $inMyHierarchy = $cfm->id === $viewer->id || $myHierarchyIds->contains($cfm->id);

        return $this->enrichCfm($cfm, $inMyHierarchy);
    }

    public function addCfm(User $viewer, array $data): array
    {
        $candidate = User::query()->whereKey($data['user_id'])->where('is_active', true)->firstOrFail();

        if ($candidate->hasRole('certified-field-mentor')) {
            throw ValidationException::withMessages([
                'user_id' => 'This member is already a Certified Field Mentor.',
            ]);
        }

        if (! $this->hierarchy->canViewMember($viewer, $candidate)) {
            throw ValidationException::withMessages([
                'user_id' => 'You do not have permission to nominate this team member.',
            ]);
        }

        $requireApproval = (bool) ($data['require_approval'] ?? false);
        $targetRank = $data['target_rank'];
        $notes = trim($data['notes'] ?? '');

        return DB::transaction(function () use ($candidate, $requireApproval, $targetRank, $notes): array {
            if (! $candidate->hasRole('certified-field-mentor')) {
                $candidate->assignRole('certified-field-mentor');
            }

            $bio = $notes !== ''
                ? $notes
                : 'Nominated for CFM mentorship duties.';

            CfmMentorProfile::updateOrCreate(
                ['user_id' => $candidate->id],
                [
                    'certification_status' => $requireApproval ? 'pending_approval' : 'certified',
                    'hierarchy_access' => 'my_hierarchy',
                    'max_apprentices' => 6,
                    'fap_completion_rate' => 0,
                    'calendar_busyness_percent' => 0,
                    'avg_apprentice_progress' => 0,
                    'recommendation_score' => 75,
                    'languages' => ['English'],
                    'specialties' => array_values(array_filter([$targetRank, 'Field Apprenticeship'])),
                    'mentor_bio' => $bio,
                    'last_mentor_activity_at' => now(),
                ]
            );

            $message = $requireApproval
                ? "{$candidate->name} was nominated as a CFM. Certification is pending admin approval and will appear as Pending Approval in the directory."
                : "{$candidate->name} was added as a Certified Field Mentor and is now available for assignments.";

            return [
                'message' => $message,
                'user_id' => $candidate->id,
                'certification_status' => $requireApproval ? 'pending_approval' : 'certified',
            ];
        });
    }

    public function assignAssociate(User $viewer, array $data): array
    {
        $associate = User::query()->whereKey($data['associate_id'])->where('is_active', true)->firstOrFail();
        $cfm = User::query()->role('certified-field-mentor')->whereKey($data['cfm_id'])->where('is_active', true)->firstOrFail();

        if ($associate->mentor_id) {
            throw ValidationException::withMessages([
                'associate_id' => 'This associate already has an assigned CFM.',
            ]);
        }

        if (! $this->hierarchy->canViewMember($viewer, $associate)) {
            throw ValidationException::withMessages([
                'associate_id' => 'You do not have permission to assign this associate.',
            ]);
        }

        $accessibleCfmIds = $this->accessibleCfmIds($viewer);
        if (! $accessibleCfmIds->contains($cfm->id)) {
            throw ValidationException::withMessages([
                'cfm_id' => 'You do not have permission to assign to this CFM.',
            ]);
        }

        $this->assertCfmLicensedForAssociate($associate, $cfm);

        $requireApproval = (bool) ($data['require_cfm_approval'] ?? false);
        if (! $this->cfmIsInViewerHierarchy($viewer, $cfm)) {
            $requireApproval = true;
        }

        $status = $requireApproval ? 'pending' : 'active';
        $startedAt = ! empty($data['start_date']) ? Carbon::parse($data['start_date']) : now();

        return DB::transaction(function () use ($viewer, $associate, $cfm, $data, $status, $startedAt): array {
            $assignment = MentorAssignment::updateOrCreate(
                [
                    'mentor_id' => $cfm->id,
                    'apprentice_id' => $associate->id,
                    'status' => $status,
                ],
                [
                    'assigned_by' => $viewer->id,
                    'started_at' => $startedAt->toDateString(),
                    'completed_at' => ! empty($data['end_date']) ? Carbon::parse($data['end_date'])->toDateString() : null,
                ]
            );

            if ($status === 'active') {
                $associate->update(['mentor_id' => $cfm->id]);
            }

            $noteBody = trim(collect([
                $data['reason'] ?? null,
                $data['notes'] ?? null,
            ])->filter()->implode("\n\n"));

            if ($noteBody !== '') {
                DB::table('mentor_notes')->insert([
                    'mentor_assignment_id' => $assignment->id,
                    'created_by' => $viewer->id,
                    'note' => $noteBody,
                    'is_private' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $cfm->loadMissing('cfmMentorProfile');
            $cfmProfile = $cfm->cfmMentorProfile;
            if ($cfmProfile) {
                $cfmProfile->update(['last_mentor_activity_at' => now()]);
            }

            $message = $status === 'pending'
                ? "{$associate->name} was submitted for CFM approval with {$cfm->name}. The apprentice count will update once the assignment is approved."
                : "{$associate->name} was assigned to {$cfm->name} for the Field Apprenticeship Program.";

            return [
                'message' => $message,
                'status' => $status,
                'assignment_id' => $assignment->id,
            ];
        });
    }

    public function accessibleCfms(User $viewer): Collection
    {
        $cfmRoleUsers = User::query()
            ->role('certified-field-mentor')
            ->with(['profile', 'rank', 'team', 'sponsor', 'cfmMentorProfile'])
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->get();

        $myHierarchyIds = $this->hierarchy->descendantsQuery($viewer)->pluck('users.id');

        $sharedIds = TeamVisibilityPermission::query()
            ->where('viewer_id', $viewer->id)
            ->where(function (Builder $query): void {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->pluck('visible_user_id');

        return $cfmRoleUsers
            ->filter(function (User $cfm) use ($viewer, $myHierarchyIds, $sharedIds): bool {
                return $this->viewerCanAccessCfm($viewer, $cfm, $myHierarchyIds, $sharedIds);
            })
            ->map(fn (User $cfm) => $this->enrichCfm($cfm, $myHierarchyIds->contains($cfm->id)))
            ->sortByDesc('recommendationScore')
            ->values();
    }

    private function accessibleCfmIds(User $viewer): Collection
    {
        $myHierarchyIds = $this->hierarchy->descendantsQuery($viewer)->pluck('users.id');

        $sharedIds = TeamVisibilityPermission::query()
            ->where('viewer_id', $viewer->id)
            ->where(function (Builder $query): void {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->pluck('visible_user_id');

        return User::query()
            ->role('certified-field-mentor')
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->get()
            ->filter(fn (User $cfm) => $this->viewerCanAccessCfm($viewer, $cfm, $myHierarchyIds, $sharedIds))
            ->pluck('id');
    }

    private function cfmIsInViewerHierarchy(User $viewer, User $cfm): bool
    {
        return $this->hierarchy->descendantsQuery($viewer)->where('users.id', $cfm->id)->exists();
    }

    private function viewerCanAccessCfm(User $viewer, User $cfm, Collection $myHierarchyIds, Collection $sharedIds): bool
    {
        if ($cfm->id === $viewer->id) {
            return true;
        }

        if ($viewer->hasAnyRole(['super-admin', 'admin'])) {
            return true;
        }

        if ($myHierarchyIds->contains($cfm->id)) {
            return true;
        }

        return $sharedIds->contains($cfm->id);
    }

    private function enrichCfm(User $cfm, bool $inMyHierarchy): array
    {
        $profile = $cfm->cfmMentorProfile ?? CfmMentorProfile::make([
            'certification_status' => 'certified',
            'hierarchy_access' => $inMyHierarchy ? 'my_hierarchy' : 'external_cfm',
            'max_apprentices' => 6,
        ]);

        $activeApprentices = User::query()
            ->where('mentor_id', $cfm->id)
            ->where('is_active', true)
            ->count();

        $pendingApprentices = MentorAssignment::query()
            ->where('mentor_id', $cfm->id)
            ->where('status', 'pending')
            ->count();

        $completedApprentices = MentorAssignment::query()
            ->where('mentor_id', $cfm->id)
            ->where('status', 'completed')
            ->count();

        $overdueTasks = UserTask::query()
            ->where('assigned_to_user_id', $cfm->id)
            ->where('status', 'overdue')
            ->count();

        $upcomingSessions = Booking::query()
            ->where('cfm_id', $cfm->id)
            ->where('starts_at', '>=', now())
            ->where('starts_at', '<=', now()->addDays(7))
            ->whereNull('cancelled_at')
            ->count();

        $nextSlot = Booking::query()
            ->where('cfm_id', $cfm->id)
            ->where('starts_at', '>=', now())
            ->whereNull('cancelled_at')
            ->orderBy('starts_at')
            ->value('starts_at');

        $maxApprentices = (int) $profile->max_apprentices;
        $loadPercent = $maxApprentices > 0
            ? (int) round(($activeApprentices / $maxApprentices) * 100)
            : 0;

        $calendarBusyness = $this->calendarBusynessPercent($cfm->id);
        $fapCompletionRate = $this->fapCompletionRateFor($cfm->id);
        $avgApprenticeProgress = $this->avgApprenticeProgressFor($cfm->id);
        $workload = $this->resolveWorkload($activeApprentices, (bool) $profile->manual_unavailable);
        $hierarchySource = $this->hierarchySourceLabel($profile->hierarchy_access, $inMyHierarchy);
        $recommendation = $this->recommendationBand(
            $profile,
            $activeApprentices,
            $overdueTasks,
            $loadPercent,
            $calendarBusyness
        );

        $apprenticeNames = User::query()
            ->where('mentor_id', $cfm->id)
            ->with(['rank', 'apprenticeshipAssignments' => fn ($q) => $q->where('mentor_id', $cfm->id)->latest()->limit(1)])
            ->limit(8)
            ->get()
            ->map(fn (User $a) => [
                'id' => $a->id,
                'name' => $a->name,
                'rank' => $a->rank?->code ?? '—',
                'status' => $a->apprenticeshipAssignments->first()?->status ?? 'active',
            ]);

        return [
            'id' => $cfm->id,
            'name' => $cfm->name,
            'email' => $cfm->email,
            'phone' => $cfm->profile?->phone ?? '—',
            'initials' => $this->initials($cfm->name),
            'rank' => $cfm->rank?->code ?? '—',
            'rankName' => $cfm->rank?->name ?? '—',
            'hierarchySource' => $hierarchySource,
            'hierarchyAccess' => $profile->hierarchy_access,
            'hierarchyNotice' => $hierarchySource === 'External CFM'
                ? 'This CFM belongs to another hierarchy. Assignment may require permission or approval.'
                : null,
            'sponsor' => $cfm->sponsor?->name ?? '—',
            'agencyOwner' => $this->agencyOwnerFor($cfm),
            'country' => $cfm->profile?->country ?? '—',
            'province' => $cfm->profile?->province ?? '—',
            'city' => $cfm->profile?->city ?? '—',
            'timezone' => $cfm->profile?->timezone ?? '—',
            'certificationStatus' => str($profile->certification_status)->replace('_', ' ')->title()->toString(),
            'activeApprentices' => $activeApprentices,
            'pendingApprentices' => $pendingApprentices,
            'completedApprentices' => $completedApprentices,
            'maxApprentices' => $maxApprentices,
            'loadPercent' => min($loadPercent, 100),
            'calendarBusyness' => $calendarBusyness,
            'fapCompletionRate' => $fapCompletionRate,
            'avgApprenticeProgress' => $avgApprenticeProgress,
            'overdueTasks' => $overdueTasks,
            'upcomingSessions' => $upcomingSessions,
            'nextSlot' => $nextSlot ? Carbon::parse($nextSlot)->format('M j, g:i A') : 'No slot listed',
            'nextSlots' => $this->nextOpenSlots($cfm->id),
            'workloadStatus' => $workload['label'],
            'workloadKey' => $workload['key'],
            'workloadColor' => $workload['color'],
            'recommendationScore' => (int) $profile->recommendation_score,
            'recommendationBand' => $recommendation['band'],
            'recommendationColor' => $recommendation['color'],
            'languages' => $profile->languages ?? [],
            'specialties' => $profile->specialties ?? [],
            'licensedJurisdictions' => $profile->licensed_jurisdictions ?? [],
            'licensedJurisdictionsLabel' => $this->licensedJurisdictionsLabel($profile->licensed_jurisdictions ?? []),
            'bio' => $profile->mentor_bio,
            'lastActivity' => $profile->last_mentor_activity_at?->diffForHumans() ?? '—',
            'inMyHierarchy' => $inMyHierarchy,
            'limitedVisibility' => $profile->hierarchy_access === 'limited_visibility',
            'apprentices' => $apprenticeNames->values()->all(),
            'apprenticeBreakdown' => $this->apprenticeBreakdownFor($cfm->id),
            'calendarPreview' => $this->calendarPreview($cfm->id),
            'activityTimeline' => $this->activityTimeline($cfm),
            'assignmentHistory' => MentorAssignment::query()
                ->where('mentor_id', $cfm->id)
                ->with('apprentice:id,name')
                ->latest()
                ->limit(5)
                ->get()
                ->map(fn (MentorAssignment $a) => [
                    'apprentice' => $a->apprentice?->name ?? 'Associate',
                    'status' => str($a->status)->title()->toString(),
                    'date' => ($a->started_at ?? $a->created_at)?->format('M j, Y') ?? '—',
                ])
                ->values()
                ->all(),
            'profileUrl' => route('team.member', $cfm),
            'calendarUrl' => route('calendar.index'),
            'messageUrl' => 'mailto:'.$cfm->email,
        ];
    }

    private function statsFrom(Collection $cfms, User $viewer): array
    {
        $activeApprenticeTotal = $cfms->sum('activeApprentices');
        $cfmCount = $cfms->count();

        $pendingFap = $this->hierarchy->descendantsQuery($viewer)
            ->whereNull('mentor_id')
            ->where('is_active', true)
            ->count();

        $avgSlots = $cfmCount > 0
            ? $cfms->avg(fn (array $cfm) => $cfm['calendarPreview']['slotsThisWeek'] ?? 0)
            : 0;

        return [
            'total' => $cfmCount,
            'available' => $cfms->where('workloadKey', 'available')->count(),
            'moderate' => $cfms->where('workloadKey', 'moderate')->count(),
            'busy' => $cfms->where('workloadKey', 'busy')->count(),
            'overloaded' => $cfms->where('workloadKey', 'overloaded')->count(),
            'myHierarchy' => $cfms->where('inMyHierarchy', true)->count(),
            'externalHierarchy' => $cfms->where('inMyHierarchy', false)->count(),
            'activeApprentices' => $activeApprenticeTotal,
            'pendingFap' => $pendingFap,
            'averageLoad' => $cfmCount > 0 ? round($activeApprenticeTotal / $cfmCount, 1) : 0,
            'fapCompletionRate' => $cfmCount > 0 ? round($cfms->avg('fapCompletionRate'), 1) : 0,
            'avgWeeklyAvailabilityHours' => (int) round($avgSlots * 1.5),
        ];
    }

    private function fapQueueFor(User $viewer): array
    {
        return $this->hierarchy->descendantsQuery($viewer)
            ->whereNull('mentor_id')
            ->where('is_active', true)
            ->with(['rank', 'profile', 'sponsor'])
            ->orderBy('name')
            ->limit(50)
            ->get()
            ->map(fn (User $user) => [
                'id' => $user->id,
                'name' => MemberDisplayName::for($user),
                'queueLabel' => MemberDisplayName::fapQueueLabelFor($user),
                'email' => $user->email,
                'rank' => $user->rank?->code ?? '—',
                'rankName' => $user->rank?->name ?? '—',
                'sponsor' => $user->sponsor?->name ?? '—',
                'city' => $user->profile?->city ?? '—',
                'country' => $user->profile?->country ?? '',
                'province' => $user->profile?->province ?? '',
                'jurisdictionKey' => filled($user->profile?->country) && filled($user->profile?->province)
                    ? LocationOptions::jurisdictionKey($user->profile->country, $user->profile->province)
                    : '',
                'timezone' => $user->profile?->timezone ?? '—',
                'profileUrl' => route('team.member', $user),
            ])
            ->values()
            ->all();
    }

    private function cfmCandidatesFor(User $viewer): array
    {
        return $this->hierarchy->descendantsQuery($viewer)
            ->where('is_active', true)
            ->with(['rank', 'profile'])
            ->orderBy('name')
            ->limit(50)
            ->get()
            ->filter(fn (User $user) => ! $user->hasRole('certified-field-mentor'))
            ->map(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'rank' => $user->rank?->code ?? '—',
                'rankName' => $user->rank?->name ?? '—',
                'city' => $user->profile?->city ?? '—',
            ])
            ->values()
            ->all();
    }

    private function apprenticeBreakdownFor(int $cfmId): array
    {
        $activeIds = User::query()
            ->where('mentor_id', $cfmId)
            ->where('is_active', true)
            ->pluck('id');

        $activeCount = $activeIds->count();

        $newThisMonth = MentorAssignment::query()
            ->where('mentor_id', $cfmId)
            ->where('started_at', '>=', now()->startOfMonth())
            ->count();

        $nearingCompletion = MentorAssignment::query()
            ->where('mentor_id', $cfmId)
            ->where('status', 'active')
            ->where('started_at', '<=', now()->subDays(75))
            ->count();

        $behindSchedule = $activeIds->isEmpty()
            ? 0
            : (int) DB::table('user_apprenticeship_progress')
                ->whereIn('user_id', $activeIds)
                ->whereIn('status', ['pending_confirmation', 'not_started'])
                ->where('updated_at', '<', now()->subDays(14))
                ->distinct()
                ->count('user_id');

        $noRecentActivity = User::query()
            ->whereIn('id', $activeIds)
            ->where('updated_at', '<', now()->subDays(14))
            ->count();

        $awaitingApproval = MentorAssignment::query()
            ->where('mentor_id', $cfmId)
            ->where('status', 'pending')
            ->count();

        return [
            'active' => $activeCount,
            'newThisMonth' => $newThisMonth,
            'nearingCompletion' => $nearingCompletion,
            'behindSchedule' => $behindSchedule,
            'noRecentActivity' => $noRecentActivity,
            'awaitingApproval' => $awaitingApproval,
        ];
    }

    private function fapCompletionRateFor(int $cfmId): float
    {
        $completed = MentorAssignment::query()
            ->where('mentor_id', $cfmId)
            ->where('status', 'completed')
            ->count();

        $active = MentorAssignment::query()
            ->where('mentor_id', $cfmId)
            ->where('status', 'active')
            ->count();

        $total = $completed + $active;

        if ($total === 0) {
            return 0.0;
        }

        return round(($completed / $total) * 100, 1);
    }

    private function avgApprenticeProgressFor(int $cfmId): int
    {
        $apprenticeIds = User::query()->where('mentor_id', $cfmId)->pluck('id');

        if ($apprenticeIds->isEmpty()) {
            return 0;
        }

        $stepCount = (int) DB::table('apprenticeship_steps')->count();

        if ($stepCount === 0) {
            return 0;
        }

        $completedSteps = (int) DB::table('user_apprenticeship_progress')
            ->whereIn('user_id', $apprenticeIds)
            ->where('status', 'completed')
            ->count();

        return (int) round(($completedSteps / ($apprenticeIds->count() * $stepCount)) * 100);
    }

    private function calendarBusynessPercent(int $cfmId): int
    {
        $booked = Booking::query()
            ->where('cfm_id', $cfmId)
            ->whereBetween('starts_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->whereNull('cancelled_at')
            ->count();

        return (int) min(100, round(($booked / self::WEEKLY_SLOT_CAPACITY) * 100));
    }

    private function resolveWorkload(int $active, bool $manualUnavailable): array
    {
        if ($manualUnavailable) {
            return ['key' => 'unavailable', 'label' => 'Unavailable', 'color' => 'slate'];
        }

        return match (true) {
            $active <= 2 => ['key' => 'available', 'label' => 'Available', 'color' => 'emerald'],
            $active <= 5 => ['key' => 'moderate', 'label' => 'Moderate', 'color' => 'amber'],
            $active <= 8 => ['key' => 'busy', 'label' => 'Busy', 'color' => 'orange'],
            default => ['key' => 'overloaded', 'label' => 'Overloaded', 'color' => 'red'],
        };
    }

    private function hierarchySourceLabel(string $access, bool $inMyHierarchy): string
    {
        if ($inMyHierarchy) {
            return 'My Hierarchy';
        }

        return match ($access) {
            'shared_access' => 'Shared Access',
            'admin_approved' => 'Admin Approved',
            'limited_visibility' => 'Limited Visibility',
            default => 'External CFM',
        };
    }

    private function recommendationBand(
        CfmMentorProfile $profile,
        int $active,
        int $overdue,
        int $loadPercent,
        int $calendarBusyness
    ): array {
        $score = (int) $profile->recommendation_score;

        if ($score >= 85 && $active <= 5 && $overdue === 0) {
            return ['band' => 'Recommended', 'color' => 'emerald'];
        }

        if ($score >= 70 && $loadPercent <= 80) {
            return ['band' => 'Good Fit', 'color' => 'amber'];
        }

        if ($score >= 50 && $loadPercent <= 100) {
            return ['band' => 'Use Caution', 'color' => 'orange'];
        }

        return ['band' => 'Not Recommended', 'color' => 'red'];
    }

    private function nextOpenSlots(int $cfmId): array
    {
        return Booking::query()
            ->where('cfm_id', $cfmId)
            ->where('starts_at', '>=', now())
            ->whereNull('cancelled_at')
            ->orderBy('starts_at')
            ->limit(3)
            ->get()
            ->map(fn (Booking $b) => Carbon::parse($b->starts_at)->format('M j, g:i A'))
            ->values()
            ->all();
    }

    private function calendarPreview(int $cfmId): array
    {
        $booked = Booking::query()
            ->where('cfm_id', $cfmId)
            ->whereBetween('starts_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->whereNull('cancelled_at')
            ->count();

        $busyness = $this->calendarBusynessPercent($cfmId);

        return [
            'slotsThisWeek' => max(0, self::WEEKLY_SLOT_CAPACITY - $booked),
            'bookedSessions' => $booked,
            'blockedDays' => $booked > 8 ? 2 : 0,
            'conflictWarning' => $booked > 10,
            'avgWeeklyAvailability' => max(0, 100 - $busyness),
        ];
    }

    private function activityTimeline(User $cfm): array
    {
        $items = [];

        $lastAssignment = MentorAssignment::query()->where('mentor_id', $cfm->id)->latest()->first();
        if ($lastAssignment) {
            $items[] = ['label' => 'Apprentice assignment updated', 'time' => $lastAssignment->updated_at?->diffForHumans() ?? '—'];
        }

        $lastBooking = Booking::query()->where('cfm_id', $cfm->id)->latest('starts_at')->first();
        if ($lastBooking) {
            $items[] = ['label' => 'Mentor session scheduled', 'time' => $lastBooking->starts_at?->diffForHumans() ?? '—'];
        }

        $lastTask = UserTask::query()->where('assigned_to_user_id', $cfm->id)->latest()->first();
        if ($lastTask) {
            $items[] = ['label' => 'Mentor task: '.$lastTask->title, 'time' => $lastTask->updated_at?->diffForHumans() ?? '—'];
        }

        if ($cfm->cfmMentorProfile?->updated_at) {
            $items[] = ['label' => 'CFM profile updated', 'time' => $cfm->cfmMentorProfile->updated_at->diffForHumans()];
        }

        return $items;
    }

    private function agencyOwnerFor(User $cfm): string
    {
        return $this->resolveAgencyOwnerUpline($cfm)?->name ?? '—';
    }

    private function resolveAgencyOwnerUpline(User $cfm): ?User
    {
        $cfm->loadMissing(['sponsor', 'team.owner']);

        if ($cfm->hasRole('agency-owner')) {
            return $cfm;
        }

        $visited = [$cfm->id];
        $sponsor = $cfm->sponsor;

        while ($sponsor) {
            if ($sponsor->hasRole('agency-owner')) {
                return $sponsor;
            }

            if (in_array($sponsor->id, $visited, true) || ! $sponsor->sponsor_id) {
                break;
            }

            $visited[] = $sponsor->id;
            $sponsor = User::query()->whereKey($sponsor->sponsor_id)->first();
        }

        $teamOwner = $cfm->team?->owner;

        if ($teamOwner?->hasRole('agency-owner')) {
            return $teamOwner;
        }

        return $teamOwner;
    }

    private function assertCfmLicensedForAssociate(User $associate, User $cfm): void
    {
        $associate->loadMissing('profile');
        $cfm->loadMissing('cfmMentorProfile');

        $country = $associate->profile?->country;
        $province = $associate->profile?->province;

        if (! filled($country) || ! filled($province)) {
            throw ValidationException::withMessages([
                'associate_id' => 'The associate must have a country and province/state on their profile before CFM assignment.',
            ]);
        }

        $licensed = $cfm->cfmMentorProfile?->licensed_jurisdictions ?? [];

        if (! LocationOptions::cfmCoversJurisdiction($licensed, $country, $province)) {
            $location = LocationOptions::formatJurisdictionLabel($country, $province);

            throw ValidationException::withMessages([
                'cfm_id' => "This CFM is not licensed in {$location}. Select a CFM licensed in the associate's province or state.",
            ]);
        }
    }

    /**
     * @param  list<string>|null  $keys
     */
    private function licensedJurisdictionsLabel(?array $keys): string
    {
        $keys = LocationOptions::normalizeLicensedJurisdictionKeys($keys);

        if ($keys === []) {
            return '—';
        }

        return implode('; ', LocationOptions::labelsForJurisdictionKeys($keys));
    }

    private function initials(string $name): string
    {
        return collect(explode(' ', $name))->filter()->take(2)
            ->map(fn (string $part) => str($part)->substr(0, 1)->upper()->toString())
            ->join('');
    }
}
