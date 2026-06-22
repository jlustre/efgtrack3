<?php

namespace App\Services;

use App\Models\CalendarScheduleBlock;
use App\Models\CalendarScheduleBlockOverride;
use App\Models\MentorAssignment;
use App\Models\User;
use App\Models\UserCalendarPreference;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

class CalendarScheduleBlockService
{
    public function __construct(private readonly CalendarShareService $calendarShare) {}

    /**
     * @return Collection<int, CalendarScheduleBlock>
     */
    public function weeklyBlocksFor(User $user): Collection
    {
        return CalendarScheduleBlock::query()
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->orderBy('weekday')
            ->orderBy('starts_at')
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * @return Collection<int, CalendarScheduleBlockOverride>
     */
    public function overridesFor(User $user): Collection
    {
        return CalendarScheduleBlockOverride::query()
            ->where('user_id', $user->id)
            ->where('block_date', '>=', now()->subMonth()->toDateString())
            ->orderBy('block_date')
            ->orderBy('starts_at')
            ->get();
    }

    /**
     * @return array<string, Collection<int, array<string, mixed>>>
     */
    public function expandedBlocksByDate(User $user, CarbonImmutable $rangeStart, CarbonImmutable $rangeEnd): array
    {
        $weekly = CalendarScheduleBlock::query()
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->get();

        $overrides = CalendarScheduleBlockOverride::query()
            ->where('user_id', $user->id)
            ->whereBetween('block_date', [$rangeStart->toDateString(), $rangeEnd->toDateString()])
            ->get()
            ->groupBy(fn (CalendarScheduleBlockOverride $override): string => $override->block_date->toDateString());

        $byDate = [];

        for ($day = $rangeStart; $day->lte($rangeEnd); $day = $day->addDay()) {
            $dateKey = $day->toDateString();
            $items = collect();

            foreach ($weekly->where('weekday', $day->dayOfWeekIso) as $block) {
                $items->push($this->formatOccurrence($block, $day, $user));
            }

            foreach ($overrides->get($dateKey, collect()) as $override) {
                if ($override->is_blocked) {
                    $items->push($this->formatOverrideOccurrence($override, $user));
                } else {
                    $items = $items->reject(function (array $item) use ($override): bool {
                        if ($override->is_all_day) {
                            return true;
                        }

                        if (! $override->starts_at || ! $override->ends_at) {
                            return false;
                        }

                        return $this->timesOverlap(
                            $item['starts_at']->format('H:i:s'),
                            $item['ends_at']->format('H:i:s'),
                            (string) $override->starts_at,
                            (string) $override->ends_at
                        );
                    });
                }
            }

            if ($items->isNotEmpty()) {
                $byDate[$dateKey] = $items->sortBy('starts_at')->values();
            }
        }

        return $byDate;
    }

    /**
     * @return array<string, Collection<int, array<string, mixed>>>
     */
    public function sharedExpandedBlocksByDate(User $viewer, CarbonImmutable $rangeStart, CarbonImmutable $rangeEnd): array
    {
        $owners = $this->calendarShare->sharedScheduleBlockOwnersFor($viewer);

        $merged = [];

        foreach ($owners as $owner) {
            foreach ($this->expandedBlocksByDate($owner, $rangeStart, $rangeEnd) as $date => $items) {
                $sharedItems = $items
                    ->filter(fn (array $item): bool => (bool) ($item['is_shared'] ?? true))
                    ->map(function (array $item) use ($owner): array {
                        $item['owner_id'] = $owner->id;
                        $item['owner_name'] = $owner->name;

                        return $item;
                    });

                if ($sharedItems->isEmpty()) {
                    continue;
                }

                $merged[$date] = ($merged[$date] ?? collect())->merge($sharedItems)->sortBy('starts_at')->values();
            }
        }

        return $merged;
    }

    public function storeWeeklyBlock(User $user, array $data): CalendarScheduleBlock
    {
        return CalendarScheduleBlock::create([
            'user_id' => $user->id,
            'block_type' => $data['block_type'],
            'label' => $data['label'] ?? null,
            'weekday' => (int) $data['weekday'],
            'starts_at' => $data['starts_at'],
            'ends_at' => $data['ends_at'],
            'is_active' => (bool) ($data['is_active'] ?? true),
            'is_shared' => (bool) ($data['is_shared'] ?? true),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ]);
    }

    public function updateWeeklyBlock(CalendarScheduleBlock $block, array $data): CalendarScheduleBlock
    {
        $block->update([
            'block_type' => $data['block_type'],
            'label' => $data['label'] ?? null,
            'weekday' => (int) $data['weekday'],
            'starts_at' => $data['starts_at'],
            'ends_at' => $data['ends_at'],
            'is_active' => (bool) ($data['is_active'] ?? true),
            'is_shared' => (bool) ($data['is_shared'] ?? true),
        ]);

        return $block->refresh();
    }

    public function storeOverride(User $user, array $data): CalendarScheduleBlockOverride
    {
        return CalendarScheduleBlockOverride::create([
            'user_id' => $user->id,
            'block_date' => $data['block_date'],
            'starts_at' => ($data['is_all_day'] ?? false) ? null : ($data['starts_at'] ?? null),
            'ends_at' => ($data['is_all_day'] ?? false) ? null : ($data['ends_at'] ?? null),
            'is_all_day' => (bool) ($data['is_all_day'] ?? false),
            'block_type' => $data['block_type'] ?? 'other',
            'label' => $data['label'] ?? null,
            'reason' => $data['reason'] ?? null,
            'is_blocked' => (bool) ($data['is_blocked'] ?? true),
            'is_shared' => (bool) ($data['is_shared'] ?? true),
        ]);
    }

    public function updateSharingPreference(User $user, bool $shareWithMentor): UserCalendarPreference
    {
        $preference = UserCalendarPreference::firstOrCreate(
            ['user_id' => $user->id],
            [
                'default_view' => 'month',
                'timezone' => $user->profile?->timezone ?? 'PST',
                'show_weekends' => true,
                'share_schedule_blocks_with_mentor' => true,
            ]
        );

        $preference->update(['share_schedule_blocks_with_mentor' => $shareWithMentor]);

        return $preference->refresh();
    }

    public function userSharesBlocksWithMentor(User $user): bool
    {
        $preference = UserCalendarPreference::query()->where('user_id', $user->id)->first();

        return $preference?->share_schedule_blocks_with_mentor ?? true;
    }

    /**
     * @return list<int>
     */
    public function apprenticeIdsForCfm(User $cfm): array
    {
        return MentorAssignment::query()
            ->where('mentor_id', $cfm->id)
            ->where('status', 'active')
            ->pluck('apprentice_id')
            ->map(fn ($id): int => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    private function formatOccurrence(CalendarScheduleBlock $block, CarbonImmutable $day, User $user): array
    {
        $startsAt = $day->setTimeFromTimeString(substr((string) $block->starts_at, 0, 8));
        $endsAt = $day->setTimeFromTimeString(substr((string) $block->ends_at, 0, 8));

        return [
            'id' => 'block-'.$block->id.'-'.$day->toDateString(),
            'source' => 'weekly',
            'block_type' => $block->block_type,
            'label' => $block->displayLabel(),
            'color' => $block->typeColor(),
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'is_all_day' => false,
            'is_shared' => $block->is_shared && $this->userSharesBlocksWithMentor($user),
            'owner_id' => $user->id,
            'owner_name' => $user->name,
        ];
    }

    private function formatOverrideOccurrence(CalendarScheduleBlockOverride $override, User $user): array
    {
        $day = CarbonImmutable::parse($override->block_date->toDateString());

        if ($override->is_all_day) {
            $startsAt = $day->startOfDay();
            $endsAt = $day->endOfDay();
        } else {
            $startsAt = $day->setTimeFromTimeString(substr((string) $override->starts_at, 0, 8));
            $endsAt = $day->setTimeFromTimeString(substr((string) $override->ends_at, 0, 8));
        }

        return [
            'id' => 'override-'.$override->id,
            'source' => 'override',
            'block_type' => $override->block_type,
            'label' => $override->displayLabel(),
            'color' => $override->typeColor(),
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'is_all_day' => $override->is_all_day,
            'is_shared' => $override->is_shared && $this->userSharesBlocksWithMentor($user),
            'owner_id' => $user->id,
            'owner_name' => $user->name,
        ];
    }

    private function timesOverlap(string $aStart, string $aEnd, string $bStart, string $bEnd): bool
    {
        return $aStart < $bEnd && $bStart < $aEnd;
    }
}
