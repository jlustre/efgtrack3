<?php



namespace App\Services;



use App\Models\OnboardingStep;
use App\Models\Prospect;
use App\Models\Rank;
use App\Models\User;
use App\Services\Fna\FnaAnalyticsService;
use App\Services\Prospects\ProspectAnalyticsService;

use Illuminate\Support\Collection;

use Illuminate\Support\Facades\DB;



class DashboardStatsService

{

    public function __construct(

        private readonly DownlineHierarchyService $hierarchy,

        private readonly ProfileCompletionService $profileCompletion,

        private readonly MemberProfileTabsService $memberProfileTabs,

        private readonly ProspectAnalyticsService $prospectAnalytics,

        private readonly FnaAnalyticsService $fnaAnalytics,

    ) {}



    /**

     * @return array{onboarding: int, licensing: int, apprenticeship: int, training: int}

     */

    public function trackerProgress(User $user): array

    {

        $user->loadMissing('profile');



        return [

            'onboarding' => $this->onboardingPercent($user),

            'licensing' => $this->licensingPercent($user),

            'apprenticeship' => $this->apprenticeshipPercent($user),

            'training' => $this->trainingPercent($user),

        ];

    }



    /**

     * @return array{team: list<array{key: string, label: string, value: string, bar: int}>, personal: list<array{key: string, label: string, value: string, bar: int}>}

     */

    public function statCards(User $viewer): array

    {

        return [

            'team' => $this->teamStatCards($viewer),

            'personal' => $this->personalStatCards($viewer),

        ];

    }



    /**

     * @return list<array{key: string, label: string, value: string, bar: int}>

     */

    public function teamStatCards(User $viewer): array

    {

        $members = $this->hierarchy->dashboardMembersQuery($viewer)->get();



        return [

            $this->card('profile', 'Team Profile Completion', $this->teamAveragePercent($members, fn (User $member) => $this->profileCompletion->percent($member))),

            $this->card('onboarding', 'Team Onboarding', $this->teamAveragePercent($members, fn (User $member) => $this->onboardingPercent($member))),

            $this->card('credentials', 'Team Licensing', $this->teamAveragePercent($members, fn (User $member) => $this->licensingPercent($member))),

            $this->card('apprenticeship', 'Team FAP', $this->teamAveragePercent($members, fn (User $member) => $this->apprenticeshipPercent($member))),

            $this->card('training', 'Team CFM Training', $this->teamAveragePercent($members, fn (User $member) => $this->trainingPercent($member))),

        ];

    }



    /**

     * @return list<array{key: string, label: string, value: string, bar: int, show_bar?: bool}>

     */

    public function personalStatCards(User $viewer): array

    {

        $viewer->loadMissing('profile');



        $cards = [];



        if ($this->shouldShowPersonalProfileCard($viewer)) {

            $cards[] = $this->card('profile', 'My Profile Completion', $this->profileCompletion->percent($viewer));

        }



        if ($this->shouldShowPersonalOnboardingCard($viewer)) {

            $cards[] = $this->card('onboarding', 'My Onboarding', $this->onboardingPercent($viewer));

        }



        if ($this->shouldShowPersonalLicensingCard($viewer)) {

            $cards[] = $this->card('credentials', 'My Licensing', $this->licensingPercent($viewer));

        }



        if ($this->shouldShowPersonalApprenticeshipCard($viewer)) {

            $cards[] = $this->card('apprenticeship', 'My FAP', $this->apprenticeshipPercent($viewer));

        }



        if ($this->shouldShowPersonalTrainingCard($viewer)) {

            $cards[] = $this->card('training', 'My CFM Training', $this->trainingPercent($viewer));

        }



        $analyticsUrl = route('team.prospects.analytics');

        $cards[] = $this->countCard('prospects', 'My Prospects', $this->prospectCount($viewer), route('team.prospects'));

        $cards[] = $this->countCard('hot_prospects', 'Hot Prospects', $this->hotProspectCount($viewer), route('team.prospects', ['prospect_interest' => 'hot']));

        $cards[] = $this->countCard('followups_due', 'Follow-Ups Due', $this->followupsDueCount($viewer), route('team.prospects.follow-ups'));

        $cards[] = $this->countCard('prospect_conversion', 'Prospect Conversion', $this->prospectConversionRate($viewer).'%', $analyticsUrl);

        $cards[] = $this->countCard('recruits', 'My Recruits', $this->recruitCount($viewer));

        $cards[] = $this->currencyCard('production', 'My Production', $this->annualProductionTotal($viewer));

        if ($viewer->can('manage fna records')) {
            $fnaSummary = $this->fnaAnalytics->summaryFor($viewer);
            $cards[] = $this->countCard(
                'fna',
                'My FNA Progress',
                "{$fnaSummary['approved_fnas']}/{$fnaSummary['total_fnas']} approved",
                route('team.fna.dashboard'),
            );
        }

        return $cards;

    }



    public function prospectCount(User $viewer): int

    {

        return Prospect::query()

            ->where('owner_id', $viewer->id)

            ->where('status', 'active')

            ->where('is_archived', false)

            ->count();

    }



    public function hotProspectCount(User $viewer): int
    {
        return $this->prospectAnalytics->hotProspectCount($viewer);
    }

    public function followupsDueCount(User $viewer): int
    {
        return $this->prospectAnalytics->followupsDueCount($viewer);
    }

    public function prospectConversionRate(User $viewer): int
    {
        return $this->prospectAnalytics->prospectConversionRate($viewer);
    }

    public function recruitCount(User $viewer): int

    {

        return $this->hierarchy->descendantsQuery($viewer)->count();

    }



    public function annualProductionTotal(User $viewer): int

    {

        return $this->memberProfileTabs->annualPremiumTotal($viewer);

    }



    public function shouldShowPersonalProfileCard(User $user): bool

    {

        return ! $this->profileCompletion->isComplete($user);

    }



    public function shouldShowPersonalOnboardingCard(User $user): bool

    {

        if ($this->userIsSfaOrAbove($user)) {

            return false;

        }



        return $this->onboardingPercent($user) < 100;

    }



    public function shouldShowPersonalLicensingCard(User $user): bool

    {

        $user->loadMissing('profile');



        if (filled($user->profile?->license_number)) {

            return false;

        }



        return $this->licensingPercent($user) < 100;

    }



    public function shouldShowPersonalApprenticeshipCard(User $user): bool

    {

        if ($this->userIsSfaOrAbove($user)) {

            return false;

        }



        return $this->apprenticeshipPercent($user) < 100;

    }



    public function shouldShowPersonalTrainingCard(User $user): bool

    {

        if ($this->userIsCfm($user)) {

            return false;

        }



        return $this->trainingPercent($user) < 100;

    }



    public function userIsSfaOrAbove(User $user): bool

    {

        $user->loadMissing('rank');



        $rank = $user->rank;



        if ($rank === null) {

            return false;

        }



        $sfaSortOrder = Rank::query()

            ->where('code', 'SFA')

            ->value('sort_order');



        if ($sfaSortOrder === null) {

            return false;

        }



        return $rank->sort_order >= $sfaSortOrder;

    }



    public function userIsCfm(User $user): bool

    {

        return $user->hasRole('certified-field-mentor');

    }



    public function onboardingPercent(User $user): int

    {

        $steps = OnboardingStep::query()

            ->applicableToCountry($user->profile?->country)

            ->where('is_active', true)

            ->get();



        return $this->checklistPercent(

            $steps->pluck('id')->all(),

            'user_onboarding_progress',

            'onboarding_step_id',

            $user->id

        );

    }



    public function licensingPercent(User $user): int

    {

        $stepIds = DB::table('licensing_steps')

            ->where('is_active', true)

            ->whereNull('deleted_at')

            ->pluck('id')

            ->all();



        return $this->checklistPercent(

            $stepIds,

            'user_licensing_progress',

            'licensing_step_id',

            $user->id

        );

    }



    public function apprenticeshipPercent(User $user): int

    {

        $stepIds = DB::table('apprenticeship_steps')

            ->join('apprenticeship_programs', 'apprenticeship_programs.id', '=', 'apprenticeship_steps.apprenticeship_program_id')

            ->where('apprenticeship_steps.is_active', true)

            ->whereNull('apprenticeship_steps.deleted_at')

            ->where('apprenticeship_programs.is_active', true)

            ->whereNull('apprenticeship_programs.deleted_at')

            ->pluck('apprenticeship_steps.id')

            ->all();



        return $this->checklistPercent(

            $stepIds,

            'user_apprenticeship_progress',

            'apprenticeship_step_id',

            $user->id

        );

    }



    public function trainingPercent(User $user): int

    {

        $lessonIds = $this->publishedTrainingLessonIds();



        if ($lessonIds === []) {

            return 0;

        }



        $completed = DB::table('training_progress')

            ->where('user_id', $user->id)

            ->whereIn('training_lesson_id', $lessonIds)

            ->where('status', 'completed')

            ->count();



        return (int) round(($completed / count($lessonIds)) * 100);

    }



    public function hasStartedTraining(User $user): bool

    {

        if ($this->trainingPercent($user) > 0) {

            return true;

        }



        return DB::table('training_progress')

            ->where('user_id', $user->id)

            ->exists();

    }



    /**

     * @return list<int>

     */

    private function publishedTrainingLessonIds(): array

    {

        return DB::table('training_lessons')

            ->join('training_modules', 'training_modules.id', '=', 'training_lessons.training_module_id')

            ->join('training_categories', 'training_categories.id', '=', 'training_modules.training_category_id')

            ->where('training_modules.is_published', true)

            ->whereNull('training_modules.deleted_at')

            ->whereNull('training_lessons.deleted_at')

            ->whereNull('training_categories.deleted_at')

            ->pluck('training_lessons.id')

            ->all();

    }



    /**

     * @param  list<int|string>  $stepIds

     */

    private function checklistPercent(array $stepIds, string $progressTable, string $foreignKey, int $userId): int

    {

        $total = count($stepIds);



        if ($total === 0) {

            return 0;

        }



        $completed = DB::table($progressTable)

            ->where('user_id', $userId)

            ->whereIn($foreignKey, $stepIds)

            ->where('status', 'completed')

            ->count();



        return (int) round(($completed / $total) * 100);

    }



    /**

     * @param  Collection<int, User>  $members

     */

    private function teamAveragePercent(Collection $members, callable $percentForMember): int

    {

        if ($members->isEmpty()) {

            return 0;

        }



        $total = $members->sum(fn (User $member): int => $percentForMember($member));



        return (int) round($total / $members->count());

    }



    /**

     * @return array{key: string, label: string, value: string, bar: int}

     */

    private function card(string $key, string $label, int $percent): array

    {

        return [

            'key' => $key,

            'label' => $label,

            'value' => $percent.'%',

            'bar' => $percent,

        ];

    }



    /**

     * @return array{key: string, label: string, value: string, bar: int, show_bar: false}

     */

    private function countCard(string $key, string $label, int|string $count, ?string $url = null): array

    {

        return [

            'key' => $key,

            'label' => $label,

            'value' => (string) $count,

            'bar' => 0,

            'show_bar' => false,

            'url' => $url,

        ];

    }



    /**

     * @return array{key: string, label: string, value: string, bar: int, show_bar: false}

     */

    private function currencyCard(string $key, string $label, int $amount): array

    {

        return [

            'key' => $key,

            'label' => $label,

            'value' => '$'.number_format($amount),

            'bar' => 0,

            'show_bar' => false,

        ];

    }

}


