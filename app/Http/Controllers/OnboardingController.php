<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\OnboardingStep;
use App\Support\ProfileLocationSql;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user()->loadMissing(['profile', 'sponsor', 'mentor']);
        $country = $user->profile?->country;

        $steps = OnboardingStep::query()
            ->applicableToCountry($country)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        $progress = DB::table('user_onboarding_progress')
            ->where('user_id', $user->id)
            ->whereIn('onboarding_step_id', $steps->pluck('id'))
            ->get()
            ->keyBy('onboarding_step_id');

        $steps = $steps->map(function (OnboardingStep $step) use ($progress): OnboardingStep {
            $step->progress = $progress->get($step->id);
            $step->status = $step->progress?->status ?? 'not_started';
            $step->is_completed = $step->status === 'completed';
            $step->is_pending = $step->status === 'pending_confirmation';
            $step->is_rejected = $step->status === 'rejected';

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
        $confirmationItems = $this->confirmationItemsFor($user);
        $needsConfirmation = $confirmationItems->count();

        return view('onboarding.index', [
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
        ]);
    }

    public function update(Request $request, OnboardingStep $step): RedirectResponse
    {
        abort_if($step->trashed() || ! $step->is_active, 404);

        $country = $request->user()->profile?->country;
        $isApplicable = OnboardingStep::query()
            ->whereKey($step->id)
            ->applicableToCountry($country)
            ->exists();

        abort_unless($isApplicable, 404);

        $completed = $request->boolean('completed');

        DB::table('user_onboarding_progress')->updateOrInsert(
            [
                'user_id' => $request->user()->id,
                'onboarding_step_id' => $step->id,
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

        return back()->with('status', $completed ? 'onboarding-item-submitted-for-confirmation' : 'onboarding-item-reopened');
    }

    public function review(Request $request, int $progress): RedirectResponse
    {
        $validated = $request->validate([
            'decision' => ['required', 'string', 'in:confirmed,rejected'],
            'review_comments' => ['nullable', 'string', 'max:2000'],
        ]);

        $record = DB::table('user_onboarding_progress')
            ->join('users', 'users.id', '=', 'user_onboarding_progress.user_id')
            ->join('onboarding_steps', 'onboarding_steps.id', '=', 'user_onboarding_progress.onboarding_step_id')
            ->where('user_onboarding_progress.id', $progress)
            ->where('user_onboarding_progress.status', 'pending_confirmation')
            ->whereNull('users.deleted_at')
            ->whereNull('onboarding_steps.deleted_at')
            ->select(
                'user_onboarding_progress.*',
                'users.sponsor_id',
                'users.mentor_id',
                'onboarding_steps.notified_parties'
            )
            ->firstOrFail();

        abort_unless($this->userCanConfirm($request->user(), $record), 403);

        $confirmed = $validated['decision'] === 'confirmed';

        DB::table('user_onboarding_progress')
            ->where('id', $progress)
            ->update([
                'status' => $confirmed ? 'completed' : 'rejected',
                'completed_at' => $confirmed ? now() : null,
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
                'review_comments' => $validated['review_comments'] ?? null,
                'updated_at' => now(),
            ]);

        return back()->with('status', $confirmed ? 'onboarding-item-confirmed' : 'onboarding-item-rejected');
    }

    private function confirmationItemsFor(User $user)
    {
        $query = DB::table('user_onboarding_progress')
            ->join('users', 'users.id', '=', 'user_onboarding_progress.user_id')
            ->join('onboarding_steps', 'onboarding_steps.id', '=', 'user_onboarding_progress.onboarding_step_id')
            ->leftJoin('profiles', 'profiles.user_id', '=', 'users.id');

        ProfileLocationSql::joinMemberCountry($query);

        return $query
            ->where('user_onboarding_progress.status', 'pending_confirmation')
            ->whereNull('users.deleted_at')
            ->whereNull('onboarding_steps.deleted_at')
            ->where('onboarding_steps.is_active', true)
            ->where('users.id', '!=', $user->id)
            ->select(
                'user_onboarding_progress.id',
                'user_onboarding_progress.submitted_at',
                'users.id as member_id',
                'users.name as member_name',
                'users.email as member_email',
                'users.sponsor_id',
                'users.mentor_id',
                ProfileLocationSql::memberCountrySelect(),
                'onboarding_steps.title',
                'onboarding_steps.description',
                'onboarding_steps.notified_parties'
            )
            ->orderBy('user_onboarding_progress.submitted_at')
            ->get()
            ->filter(fn ($item) => $this->userCanConfirm($user, $item))
            ->values();
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
}
