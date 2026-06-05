<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TrackerChecklistController extends Controller
{
    public function licensing(Request $request): View
    {
        return $this->show($request, $this->config('licensing'));
    }

    public function updateLicensing(Request $request, int $step): RedirectResponse
    {
        return $this->update($request, $this->config('licensing'), $step);
    }

    public function reviewLicensing(Request $request, int $progress): RedirectResponse
    {
        return $this->review($request, $this->config('licensing'), $progress);
    }

    public function apprenticeship(Request $request): View
    {
        return $this->show($request, $this->config('apprenticeship'));
    }

    public function updateApprenticeship(Request $request, int $step): RedirectResponse
    {
        return $this->update($request, $this->config('apprenticeship'), $step);
    }

    public function reviewApprenticeship(Request $request, int $progress): RedirectResponse
    {
        return $this->review($request, $this->config('apprenticeship'), $progress);
    }

    public function cfmTraining(Request $request): View
    {
        return $this->show($request, $this->config('cfm-training'));
    }

    public function updateCfmTraining(Request $request, int $step): RedirectResponse
    {
        return $this->update($request, $this->config('cfm-training'), $step);
    }

    public function reviewCfmTraining(Request $request, int $progress): RedirectResponse
    {
        return $this->review($request, $this->config('cfm-training'), $progress);
    }

    private function show(Request $request, array $config): View
    {
        $user = $request->user()->loadMissing(['profile', 'sponsor', 'mentor']);
        $steps = $this->stepsQuery($config)->get();

        $progress = DB::table($config['progressTable'])
            ->where('user_id', $user->id)
            ->whereIn($config['foreignKey'], $steps->pluck('id'))
            ->get()
            ->keyBy($config['foreignKey']);

        $steps = $steps->map(function (object $step) use ($progress, $config): object {
            $step->progress = $progress->get($step->id);
            $step->status = $step->progress?->status ?? 'not_started';
            $step->is_completed = $step->status === 'completed';
            $step->is_pending = $step->status === 'pending_confirmation';
            $step->is_rejected = $step->status === 'rejected';
            $step->is_required = (bool) $step->is_required;
            $step->update_route = $config['updateRoute'];

            return $step;
        });

        $total = $steps->count();
        $completed = $steps->where('is_completed', true)->count();
        $pending = $steps->where('is_pending', true)->count();
        $rejected = $steps->where('is_rejected', true)->count();
        $requiredTotal = $steps->where('is_required', true)->count();
        $requiredCompleted = $steps
            ->where('is_required', true)
            ->where('is_completed', true)
            ->count();
        $optionalTotal = $total - $requiredTotal;
        $optionalCompleted = $completed - $requiredCompleted;
        $remaining = max(0, $total - $completed);
        $percent = $total > 0 ? (int) round(($completed / $total) * 100) : 0;
        $requiredPercent = $requiredTotal > 0 ? (int) round(($requiredCompleted / $requiredTotal) * 100) : 0;
        $confirmationItems = $this->confirmationItemsFor($user, $config);
        $needsConfirmation = $confirmationItems->count();

        return view($config['view'], [
            'user' => $user,
            'steps' => $steps,
            'confirmationItems' => $confirmationItems,
            'stats' => compact(
                'total',
                'completed',
                'pending',
                'rejected',
                'needsConfirmation',
                'remaining',
                'percent',
                'requiredTotal',
                'requiredCompleted',
                'requiredPercent',
                'optionalTotal',
                'optionalCompleted'
            ),
            'tracker' => $config,
        ]);
    }

    private function update(Request $request, array $config, int $step): RedirectResponse
    {
        abort_unless($this->activeStepExists($config, $step), 404);

        $completed = $request->boolean('completed');

        DB::table($config['progressTable'])->updateOrInsert(
            [
                'user_id' => $request->user()->id,
                $config['foreignKey'] => $step,
            ],
            [
                'status' => $completed ? 'pending_confirmation' : 'not_started',
                'submitted_at' => $completed ? now() : null,
                'completed_at' => null,
                'reviewed_by' => null,
                'reviewed_at' => null,
                'review_comments' => null,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        return back()->with('status', $completed ? $config['statusPrefix'].'-item-submitted-for-confirmation' : $config['statusPrefix'].'-item-reopened');
    }

    private function review(Request $request, array $config, int $progress): RedirectResponse
    {
        $validated = $request->validate([
            'decision' => ['required', 'string', 'in:confirmed,rejected'],
            'review_comments' => ['nullable', 'string', 'max:2000'],
        ]);

        $record = DB::table($config['progressTable'])
            ->join('users', 'users.id', '=', $config['progressTable'].'.user_id')
            ->join($config['stepTable'], $config['stepTable'].'.id', '=', $config['progressTable'].'.'.$config['foreignKey'])
            ->where($config['progressTable'].'.id', $progress)
            ->where($config['progressTable'].'.status', 'pending_confirmation')
            ->whereNull('users.deleted_at')
            ->whereNull($config['stepTable'].'.deleted_at')
            ->where($config['stepTable'].'.is_active', true)
            ->select(
                $config['progressTable'].'.*',
                'users.sponsor_id',
                'users.mentor_id',
                $config['stepTable'].'.notified_parties'
            )
            ->firstOrFail();

        abort_unless($this->userCanConfirm($request->user(), $record), 403);

        $confirmed = $validated['decision'] === 'confirmed';

        DB::table($config['progressTable'])
            ->where('id', $progress)
            ->update([
                'status' => $confirmed ? 'completed' : 'rejected',
                'completed_at' => $confirmed ? now() : null,
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
                'review_comments' => $validated['review_comments'] ?? null,
                'updated_at' => now(),
            ]);

        return back()->with('status', $confirmed ? $config['statusPrefix'].'-item-confirmed' : $config['statusPrefix'].'-item-rejected');
    }

    private function confirmationItemsFor(User $user, array $config)
    {
        return DB::table($config['progressTable'])
            ->join('users', 'users.id', '=', $config['progressTable'].'.user_id')
            ->join($config['stepTable'], $config['stepTable'].'.id', '=', $config['progressTable'].'.'.$config['foreignKey'])
            ->leftJoin('profiles', 'profiles.user_id', '=', 'users.id')
            ->leftJoin('countries', 'countries.id', '=', 'profiles.country_id')
            ->where($config['progressTable'].'.status', 'pending_confirmation')
            ->whereNull('users.deleted_at')
            ->whereNull($config['stepTable'].'.deleted_at')
            ->where($config['stepTable'].'.is_active', true)
            ->where('users.id', '!=', $user->id)
            ->select(
                $config['progressTable'].'.id',
                $config['progressTable'].'.submitted_at',
                'users.id as member_id',
                'users.name as member_name',
                'users.email as member_email',
                'users.sponsor_id',
                'users.mentor_id',
                'countries.name as member_country',
                $config['stepTable'].'.title',
                $config['stepTable'].'.description',
                $config['stepTable'].'.notified_parties'
            )
            ->orderBy($config['progressTable'].'.submitted_at')
            ->get()
            ->filter(fn (object $item) => $this->userCanConfirm($user, $item))
            ->values();
    }

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
                ->select(
                    'apprenticeship_steps.id',
                    'apprenticeship_steps.title',
                    'apprenticeship_steps.description',
                    'apprenticeship_steps.sort_order',
                    'apprenticeship_steps.responsible_parties',
                    'apprenticeship_steps.notified_parties',
                    'apprenticeship_programs.name as group_label'
                )
                ->selectRaw('1 as is_required');
        } else {
            $query->select(
                $config['stepTable'].'.id',
                $config['stepTable'].'.title',
                $config['stepTable'].'.description',
                $config['stepTable'].'.sort_order',
                $config['stepTable'].'.is_required',
                $config['stepTable'].'.responsible_parties',
                $config['stepTable'].'.notified_parties'
            );
        }

        return $query
            ->orderBy($config['stepTable'].'.sort_order')
            ->orderBy($config['stepTable'].'.title');
    }

    private function activeStepExists(array $config, int $step): bool
    {
        return $this->stepsQuery($config)
            ->where($config['stepTable'].'.id', $step)
            ->exists();
    }

    private function userCanConfirm(User $user, object $item): bool
    {
        $notifiedParties = collect(explode(',', (string) $item->notified_parties))
            ->map(fn (string $party) => strtoupper(trim($party)))
            ->filter()
            ->values();

        if ($notifiedParties->contains('SP') && (int) $item->sponsor_id === $user->id) {
            return true;
        }

        if ($notifiedParties->contains('CFM') && (int) $item->mentor_id === $user->id) {
            return true;
        }

        $roleMap = [
            'AO' => 'agency-owner',
            'TL' => 'team-leader',
            'TR' => 'trainer',
        ];

        foreach ($roleMap as $party => $role) {
            if ($notifiedParties->contains($party) && $user->hasRole($role)) {
                return true;
            }
        }

        return $user->hasAnyRole(['super-admin', 'admin']);
    }

    private function config(string $key): array
    {
        return match ($key) {
            'licensing' => [
                'key' => 'licensing',
                'view' => 'licensing.index',
                'eyebrow' => 'Licensing Tracker',
                'title' => 'Move from preparation to approved field readiness',
                'description' => 'Track licensing milestones, course progress, exam readiness, application status, and leadership confirmation.',
                'checklistTitle' => 'Licensing Checklist',
                'checklistDescription' => 'Submit completed licensing milestones for sponsor, mentor, or agency confirmation.',
                'emptyTitle' => 'No licensing items available',
                'emptyDescription' => 'Your licensing checklist will appear when active licensing steps are available.',
                'itemCountLabel' => 'licensing items',
                'completedLabel' => 'licensing milestones complete',
                'graphLabel' => 'Overall licensing',
                'requiredLabel' => 'Required licensing',
                'statusPrefix' => 'licensing',
                'stepTable' => 'licensing_steps',
                'progressTable' => 'user_licensing_progress',
                'foreignKey' => 'licensing_step_id',
                'indexRoute' => 'licensing.index',
                'updateRoute' => 'licensing.update',
                'reviewRoute' => 'licensing.review',
            ],
            'apprenticeship' => [
                'key' => 'apprenticeship',
                'view' => 'apprenticeship.index',
                'eyebrow' => 'Field Apprenticeship',
                'title' => 'Build confidence through guided field activity',
                'description' => 'Track apprentice readiness, mentor-guided practice, supervised appointments, and FAP completion approval.',
                'checklistTitle' => 'Field Apprenticeship Checklist',
                'checklistDescription' => 'Submit FAP milestones for sponsor, CFM, trainer, or agency confirmation.',
                'emptyTitle' => 'No apprenticeship items available',
                'emptyDescription' => 'Your FAP checklist will appear when active apprenticeship steps are available.',
                'itemCountLabel' => 'FAP items',
                'completedLabel' => 'FAP milestones complete',
                'graphLabel' => 'Overall apprenticeship',
                'requiredLabel' => 'Required FAP completion',
                'statusPrefix' => 'apprenticeship',
                'stepTable' => 'apprenticeship_steps',
                'progressTable' => 'user_apprenticeship_progress',
                'foreignKey' => 'apprenticeship_step_id',
                'indexRoute' => 'apprenticeship.index',
                'updateRoute' => 'apprenticeship.update',
                'reviewRoute' => 'apprenticeship.review',
            ],
            'cfm-training' => [
                'key' => 'cfm-training',
                'view' => 'cfm-training.index',
                'eyebrow' => 'CFM Training',
                'title' => 'Prepare to mentor apprentices with excellence',
                'description' => 'Track Certified Field Mentor training modules, coaching standards, mentor documentation, and certification readiness.',
                'checklistTitle' => 'CFM Training Checklist',
                'checklistDescription' => 'Submit CFM training modules for sponsor, trainer, or agency confirmation.',
                'emptyTitle' => 'No CFM training items available',
                'emptyDescription' => 'Your CFM training checklist will appear when active training modules are available.',
                'itemCountLabel' => 'CFM training items',
                'completedLabel' => 'CFM modules complete',
                'graphLabel' => 'Overall CFM training',
                'requiredLabel' => 'Required CFM training',
                'statusPrefix' => 'cfm-training',
                'stepTable' => 'cfm_training_modules',
                'progressTable' => 'cfm_training_progress',
                'foreignKey' => 'cfm_training_module_id',
                'indexRoute' => 'cfm-training.index',
                'updateRoute' => 'cfm-training.update',
                'reviewRoute' => 'cfm-training.review',
            ],
        };
    }
}
