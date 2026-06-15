<?php

namespace App\Http\Controllers;

use App\Models\Prospect;
use App\Models\ProspectActivity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProspectActivityController extends Controller
{
    public function index(Request $request, Prospect $prospect): JsonResponse
    {
        $this->authorize('viewAny', [ProspectActivity::class, $prospect]);

        $activities = $prospect->activities()
            ->with('user:id,name')
            ->orderByDesc('occurred_at')
            ->get()
            ->map(fn (ProspectActivity $activity) => $this->serializeActivity($activity));

        return response()->json(['activities' => $activities]);
    }

    public function store(Request $request, Prospect $prospect): JsonResponse
    {
        $this->authorize('create', [ProspectActivity::class, $prospect]);

        $validated = $this->validatedActivityData($request);

        $activity = $prospect->activities()->create([
            ...$validated,
            'user_id' => $request->user()->id,
        ]);

        $this->syncProspectContactFields($prospect, $activity);

        return response()->json([
            'activity' => $this->serializeActivity($activity->load('user:id,name')),
            'message' => 'Activity logged.',
        ], 201);
    }

    public function update(Request $request, Prospect $prospect, ProspectActivity $activity): JsonResponse
    {
        abort_unless($activity->prospect_id === $prospect->id, 404);

        $this->authorize('update', $activity);

        $validated = $this->validatedActivityData($request);

        $activity->update($validated);

        $this->syncProspectContactFields($prospect->fresh(), $activity->fresh());

        return response()->json([
            'activity' => $this->serializeActivity($activity->load('user:id,name')),
            'message' => 'Activity updated.',
        ]);
    }

    public function destroy(Prospect $prospect, ProspectActivity $activity): JsonResponse
    {
        abort_unless($activity->prospect_id === $prospect->id, 404);

        $this->authorize('delete', $activity);

        $activity->delete();

        return response()->json(['message' => 'Activity deleted.']);
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedActivityData(Request $request): array
    {
        return $request->validate([
            'activity_type' => ['required', 'string', 'in:'.implode(',', array_keys(ProspectActivity::TYPES))],
            'subject' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'occurred_at' => ['required', 'date'],
            'outcome' => ['nullable', 'string', 'max:255'],
            'next_action' => ['nullable', 'string', 'max:1000'],
            'next_follow_up_at' => ['nullable', 'date'],
        ]);
    }

    private function syncProspectContactFields(Prospect $prospect, ProspectActivity $activity): void
    {
        $updates = [];

        if ($activity->occurred_at && (
            ! $prospect->last_contacted_at
            || $activity->occurred_at->greaterThan($prospect->last_contacted_at)
        )) {
            $updates['last_contacted_at'] = $activity->occurred_at;
        }

        if ($activity->next_follow_up_at) {
            $updates['next_follow_up_at'] = $activity->next_follow_up_at;
        }

        if ($updates !== []) {
            $prospect->update($updates);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function serializeActivity(ProspectActivity $activity): array
    {
        return [
            'id' => $activity->id,
            'activity_type' => $activity->activity_type,
            'activity_type_label' => ProspectActivity::TYPES[$activity->activity_type] ?? str($activity->activity_type)->title(),
            'subject' => $activity->subject,
            'notes' => $activity->notes,
            'occurred_at' => $activity->occurred_at?->toIso8601String(),
            'occurred_at_label' => $activity->occurred_at?->format('M j, Y g:i A'),
            'outcome' => $activity->outcome,
            'next_action' => $activity->next_action,
            'next_follow_up_at' => $activity->next_follow_up_at?->toIso8601String(),
            'next_follow_up_at_label' => $activity->next_follow_up_at?->format('M j, Y g:i A'),
            'user_name' => $activity->user?->name,
            'can_edit' => auth()->user()?->can('update', $activity) ?? false,
            'can_delete' => auth()->user()?->can('delete', $activity) ?? false,
        ];
    }
}
