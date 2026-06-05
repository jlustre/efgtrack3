<?php

namespace App\Services;

use App\Models\OnboardingStep;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardStatsService
{
    /**
     * @return array{
     *     onboarding: int,
     *     licensing: int,
     *     apprenticeship: int,
     *     training: int,
     * }
     */
    public function trackerProgress(User $user): array
    {
        $user->loadMissing('profile');

        return [
            'onboarding' => $this->onboardingPercent($user),
            'licensing' => $this->checklistPercent($user, $this->licensingConfig()),
            'apprenticeship' => $this->checklistPercent($user, $this->apprenticeshipConfig()),
            'training' => $this->trainingPercent($user),
        ];
    }

    /**
     * @param  array{
     *     percent: int,
     *     is_complete: bool,
     *     fields: list<array{key: string, label: string, filled: bool}>
     * }  $profileCompletion
     * @return list<array{
     *     label: string,
     *     value: string,
     *     bar: int,
     *     interactive?: bool,
     * }>
     */
    public function statCards(User $user, array $profileCompletion): array
    {
        $progress = $this->trackerProgress($user);

        return [
            [
                'label' => 'My Profile',
                'value' => $profileCompletion['percent'].'%',
                'bar' => $profileCompletion['percent'],
                'interactive' => ! $profileCompletion['is_complete'],
            ],
            [
                'label' => 'My Onboarding',
                'value' => $progress['onboarding'].'%',
                'bar' => $progress['onboarding'],
            ],
            [
                'label' => 'My Credentials',
                'value' => $progress['licensing'].'%',
                'bar' => $progress['licensing'],
            ],
            [
                'label' => 'My Apprenticeship',
                'value' => $progress['apprenticeship'].'%',
                'bar' => $progress['apprenticeship'],
            ],
            [
                'label' => 'My Trainings',
                'value' => $progress['training'].'%',
                'bar' => $progress['training'],
            ],
        ];
    }

    private function onboardingPercent(User $user): int
    {
        $stepIds = OnboardingStep::query()
            ->applicableToCountry($user->profile?->country)
            ->where('is_active', true)
            ->pluck('id');

        return $this->percentForSteps(
            'user_onboarding_progress',
            'onboarding_step_id',
            $user->id,
            $stepIds
        );
    }

    /**
     * @param  array{
     *     stepTable: string,
     *     progressTable: string,
     *     foreignKey: string,
     *     key: string,
     * }  $config
     */
    private function checklistPercent(User $user, array $config): int
    {
        $stepIds = $this->stepsQuery($config)->pluck('id');

        return $this->percentForSteps(
            $config['progressTable'],
            $config['foreignKey'],
            $user->id,
            $stepIds
        );
    }

    private function trainingPercent(User $user): int
    {
        $lessonIds = DB::table('training_lessons')
            ->join('training_modules', 'training_modules.id', '=', 'training_lessons.training_module_id')
            ->join('training_categories', 'training_categories.id', '=', 'training_modules.training_category_id')
            ->where('training_modules.is_published', true)
            ->whereNull('training_modules.deleted_at')
            ->whereNull('training_lessons.deleted_at')
            ->whereNull('training_categories.deleted_at')
            ->pluck('training_lessons.id');

        return $this->percentForSteps(
            'training_progress',
            'training_lesson_id',
            $user->id,
            $lessonIds
        );
    }

    /**
     * @param  \Illuminate\Support\Collection<int, int>|list<int>  $stepIds
     */
    private function percentForSteps(string $progressTable, string $foreignKey, int $userId, $stepIds): int
    {
        $total = collect($stepIds)->count();

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
     * @param  array{
     *     stepTable: string,
     *     progressTable: string,
     *     foreignKey: string,
     *     key: string,
     * }  $config
     */
    private function stepsQuery(array $config)
    {
        $query = DB::table($config['stepTable'])
            ->where($config['stepTable'].'.is_active', true)
            ->whereNull($config['stepTable'].'.deleted_at');

        if ($config['key'] === 'apprenticeship') {
            $query
                ->join('apprenticeship_programs', 'apprenticeship_programs.id', '=', 'apprenticeship_steps.apprenticeship_program_id')
                ->where('apprenticeship_programs.is_active', true)
                ->whereNull('apprenticeship_programs.deleted_at')
                ->select('apprenticeship_steps.id');
        } else {
            $query->select($config['stepTable'].'.id');
        }

        return $query;
    }

    /**
     * @return array{
     *     key: string,
     *     stepTable: string,
     *     progressTable: string,
     *     foreignKey: string,
     * }
     */
    private function licensingConfig(): array
    {
        return [
            'key' => 'licensing',
            'stepTable' => 'licensing_steps',
            'progressTable' => 'user_licensing_progress',
            'foreignKey' => 'licensing_step_id',
        ];
    }

    /**
     * @return array{
     *     key: string,
     *     stepTable: string,
     *     progressTable: string,
     *     foreignKey: string,
     * }
     */
    private function apprenticeshipConfig(): array
    {
        return [
            'key' => 'apprenticeship',
            'stepTable' => 'apprenticeship_steps',
            'progressTable' => 'user_apprenticeship_progress',
            'foreignKey' => 'apprenticeship_step_id',
        ];
    }
}
