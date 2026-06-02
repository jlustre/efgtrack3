<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DownlineHierarchyService
{
    public function rebuild(): void
    {
        DB::transaction(function (): void {
            DB::table('user_hierarchy_paths')->delete();
            DB::table('sponsor_relationships')->whereNull('deleted_at')->delete();

            User::query()
                ->select(['id', 'sponsor_id'])
                ->orderBy('id')
                ->chunkById(100, function (Collection $users): void {
                    foreach ($users as $user) {
                        DB::table('user_hierarchy_paths')->updateOrInsert(
                            ['ancestor_id' => $user->id, 'descendant_id' => $user->id],
                            ['depth' => 0, 'created_at' => now(), 'updated_at' => now()]
                        );

                        if (! $user->sponsor_id) {
                            continue;
                        }

                        DB::table('sponsor_relationships')->updateOrInsert(
                            [
                                'sponsor_id' => $user->sponsor_id,
                                'member_id' => $user->id,
                                'status' => 'active',
                            ],
                            [
                                'started_at' => $user->joined_at ?? now(),
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]
                        );
                    }
                });

            User::query()
                ->whereNotNull('sponsor_id')
                ->select(['id', 'sponsor_id'])
                ->orderBy('id')
                ->chunkById(100, function (Collection $users): void {
                    foreach ($users as $user) {
                        $ancestorPaths = DB::table('user_hierarchy_paths')
                            ->where('descendant_id', $user->sponsor_id)
                            ->get(['ancestor_id', 'depth']);

                        foreach ($ancestorPaths as $path) {
                            DB::table('user_hierarchy_paths')->updateOrInsert(
                                ['ancestor_id' => $path->ancestor_id, 'descendant_id' => $user->id],
                                ['depth' => $path->depth + 1, 'created_at' => now(), 'updated_at' => now()]
                            );
                        }
                    }
                });
        });
    }

    public function descendantsQuery(User $root, bool $includeSelf = false): Builder
    {
        return User::query()
            ->select('users.*')
            ->join('user_hierarchy_paths', 'users.id', '=', 'user_hierarchy_paths.descendant_id')
            ->where('user_hierarchy_paths.ancestor_id', $root->id)
            ->when(! $includeSelf, fn (Builder $query) => $query->where('user_hierarchy_paths.depth', '>', 0));
    }

    public function directRecruitsQuery(User $root): Builder
    {
        return User::query()->where('sponsor_id', $root->id);
    }

    public function visibleMembersQuery(User $viewer): Builder
    {
        if ($viewer->hasAnyRole(['super-admin', 'admin']) || $viewer->hasPermissionTo('view all teams')) {
            return User::query();
        }

        $query = $this->descendantsQuery($viewer, includeSelf: true);

        if ($viewer->hasPermissionTo('view direct downline') && ! $viewer->hasPermissionTo('view full downline')) {
            $query->where('user_hierarchy_paths.depth', '<=', 1);
        }

        return $query;
    }

    public function canViewMember(User $viewer, User $member): bool
    {
        if ($viewer->id === $member->id || $viewer->hasAnyRole(['super-admin', 'admin']) || $viewer->hasPermissionTo('view all teams')) {
            return true;
        }

        if ($viewer->mentorAssignments()->where('apprentice_id', $member->id)->where('status', 'active')->exists()) {
            return true;
        }

        if ($viewer->teamVisibilityGrants()
            ->where('visible_user_id', $member->id)
            ->where(fn ($query) => $query->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->exists()) {
            return true;
        }

        $query = DB::table('user_hierarchy_paths')
            ->where('ancestor_id', $viewer->id)
            ->where('descendant_id', $member->id);

        if (! $viewer->hasPermissionTo('view full downline')) {
            $query->where('depth', '<=', 1);
        }

        return $query->exists();
    }

    public function memberMetrics(User $member): array
    {
        $totalDownline = DB::table('user_hierarchy_paths')
            ->where('ancestor_id', $member->id)
            ->where('depth', '>', 0)
            ->count();

        return [
            'direct_recruits' => User::where('sponsor_id', $member->id)->count(),
            'total_downline' => $totalDownline,
            'prospects' => DB::table('prospects')->where('owner_id', $member->id)->whereNull('deleted_at')->count(),
        ];
    }

    public function progressSummary(User $member): array
    {
        return [
            'licensing' => $this->percentComplete('user_licensing_progress', $member->id),
            'onboarding' => $this->percentComplete('user_onboarding_progress', $member->id),
            'training' => $this->percentComplete('training_progress', $member->id),
            'apprenticeship' => $this->percentComplete('user_apprenticeship_progress', $member->id),
            'rank' => $this->percentComplete('user_rank_progress', $member->id),
        ];
    }

    public function percentComplete(string $table, int $userId): int
    {
        $total = DB::table($table)->where('user_id', $userId)->count();

        if ($total === 0) {
            return 0;
        }

        $complete = DB::table($table)
            ->where('user_id', $userId)
            ->whereIn('status', ['completed', 'confirmed', 'approved'])
            ->count();

        return (int) round(($complete / $total) * 100);
    }
}
