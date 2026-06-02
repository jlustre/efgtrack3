<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\DownlineHierarchyService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class DownlineController extends Controller
{
    public function __construct(private readonly DownlineHierarchyService $hierarchy)
    {
    }

    public function index(Request $request): View
    {
        $user = $request->user();
        $members = $this->filteredMembers($request, $this->hierarchy->visibleMembersQuery($user));

        return view('team.downline.index', [
            'stats' => $this->stats($user),
            'rankDistribution' => $this->rankDistribution($user),
            'countryDistribution' => $this->countryDistribution($user),
            'members' => $members->paginate(10)->withQueryString(),
        ]);
    }

    public function tree(Request $request, ?User $user = null): View
    {
        $root = $user ?? $request->user();
        abort_unless($this->hierarchy->canViewMember($request->user(), $root), 403);

        $children = $this->hierarchy->directRecruitsQuery($root)
            ->with(['profile', 'rank', 'sponsor', 'mentor'])
            ->withCount(['sponsoredMembers as direct_recruits_count', 'prospects'])
            ->orderBy('name')
            ->get()
            ->map(fn (User $member) => $this->memberCard($member, $request->user()));

        return view('team.downline.tree', [
            'root' => $this->memberCard($root->loadMissing(['profile', 'rank', 'sponsor', 'mentor']), $request->user()),
            'children' => $children,
            'filters' => $this->filterOptions($request->user()),
        ]);
    }

    public function orgChart(Request $request, ?User $user = null): View
    {
        $root = $user ?? $request->user();
        abort_unless($this->hierarchy->canViewMember($request->user(), $root), 403);

        $leaders = $this->hierarchy->directRecruitsQuery($root)
            ->with(['profile', 'rank', 'roles'])
            ->withCount(['sponsoredMembers as direct_recruits_count'])
            ->orderBy('name')
            ->get()
            ->map(fn (User $member) => [
                ...$this->memberCard($member),
                'role' => $member->roles->pluck('name')->first() ?? 'member',
                'active_associates' => $this->hierarchy->descendantsQuery($member)->where('is_active', true)->count(),
                'licensed_associates' => $this->licensedCount($member),
                'pending_licensing' => $this->pendingLicensingCount($member),
            ]);

        return view('team.downline.org-chart', [
            'root' => $this->memberCard($root->loadMissing(['profile', 'rank', 'roles'])),
            'leaders' => $leaders,
            'branchSummary' => $this->stats($root),
        ]);
    }

    public function table(Request $request): View
    {
        $members = $this->filteredMembers($request, $this->hierarchy->visibleMembersQuery($request->user()));

        return view('team.downline.table', [
            'members' => $members->paginate(15)->withQueryString(),
            'filters' => $this->filterOptions($request->user()),
        ]);
    }

    public function member(Request $request, User $user): View
    {
        abort_unless($this->hierarchy->canViewMember($request->user(), $user), 403);

        $user->load(['profile', 'rank', 'team', 'sponsor', 'mentor', 'roles']);

        return view('team.downline.member', [
            'member' => $user,
            'metrics' => $this->hierarchy->memberMetrics($user),
            'progress' => $this->progressSummary($user),
            'canSeeSensitive' => $request->user()->can('viewSensitive', $user),
        ]);
    }

    public function export(Request $request): Response
    {
        abort_unless($request->user()->hasAnyPermission(['export team data', 'view all teams']), 403);

        $rows = $this->filteredMembers($request, $this->hierarchy->visibleMembersQuery($request->user()))
            ->with(['profile', 'rank', 'sponsor', 'mentor'])
            ->limit(1000)
            ->get();

        $csv = collect([
            ['Name', 'Email', 'Rank', 'Sponsor', 'CFM', 'Country', 'City', 'Status', 'Joined At', 'Direct Recruits', 'Total Downline'],
        ])->merge($rows->map(function (User $member): array {
            $metrics = $this->hierarchy->memberMetrics($member);

            return [
                $member->name,
                $member->email,
                $member->rank?->code,
                $member->sponsor?->name,
                $member->mentor?->name,
                $member->profile?->country,
                $member->profile?->city,
                $member->is_active ? 'Active' : 'Inactive',
                $member->joined_at?->toDateString(),
                $metrics['direct_recruits'],
                $metrics['total_downline'],
            ];
        }))->map(fn (array $row): string => collect($row)->map(fn ($value): string => '"'.str_replace('"', '""', (string) $value).'"')->implode(','))
            ->implode("\n");

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="efgtrack-downline.csv"',
        ]);
    }

    private function filteredMembers(Request $request, Builder $query): Builder
    {
        return $query
            ->with(['profile', 'rank', 'sponsor', 'mentor'])
            ->withCount(['sponsoredMembers as direct_recruits_count', 'prospects'])
            ->when($request->filled('search'), function (Builder $query) use ($request): void {
                $search = trim((string) $request->string('search'));

                $query->where(function (Builder $query) use ($search): void {
                    $query->where('users.name', 'like', "%{$search}%")
                        ->orWhere('users.email', 'like', "%{$search}%")
                        ->orWhereHas('profile', fn (Builder $query) => $query->where('phone', 'like', "%{$search}%"));
                });
            })
            ->when($request->filled('rank_id'), fn (Builder $query) => $query->where('users.rank_id', $request->integer('rank_id')))
            ->when($request->filled('country'), fn (Builder $query) => $query->whereHas('profile', fn (Builder $query) => $query->where('country', $request->string('country'))))
            ->when($request->filled('status'), fn (Builder $query) => $query->where('users.is_active', $request->string('status') === 'active'))
            ->when($request->filled('joined_from'), fn (Builder $query) => $query->whereDate('users.joined_at', '>=', $request->date('joined_from')))
            ->when($request->filled('joined_to'), fn (Builder $query) => $query->whereDate('users.joined_at', '<=', $request->date('joined_to')))
            ->orderBy('users.name');
    }

    private function stats(User $root): array
    {
        $members = $this->hierarchy->descendantsQuery($root, includeSelf: false);
        $total = (clone $members)->count();

        return [
            'total_team' => $total,
            'direct_recruits' => $this->hierarchy->directRecruitsQuery($root)->count(),
            'active_associates' => (clone $members)->where('users.is_active', true)->count(),
            'licensed_associates' => $this->licensedCount($root),
            'new_this_month' => (clone $members)->whereMonth('users.joined_at', now()->month)->whereYear('users.joined_at', now()->year)->count(),
            'pending_licensing' => $this->pendingLicensingCount($root),
            'cfm_assigned' => (clone $members)->whereNotNull('users.mentor_id')->count(),
            'cfm_unassigned' => (clone $members)->whereNull('users.mentor_id')->count(),
            'training_average' => $total > 0 ? round((clone $members)->get()->avg(fn (User $member) => $this->progressSummary($member)['training'])) : 0,
        ];
    }

    private function memberCard(User $member, ?User $viewer = null): array
    {
        $metrics = $this->hierarchy->memberMetrics($member);
        $progress = $this->progressSummary($member);
        $uplineTreeUrl = null;

        if ($viewer && $member->sponsor_id && $member->sponsor) {
            $uplineTreeUrl = $this->hierarchy->canViewMember($viewer, $member->sponsor)
                ? route('team.member.tree', $member->sponsor_id)
                : null;
        }

        return [
            'id' => $member->id,
            'sponsor_id' => $member->sponsor_id,
            'name' => $member->name,
            'email' => $member->email,
            'avatar' => str($member->name)->explode(' ')->map(fn ($part) => str($part)->substr(0, 1))->take(2)->implode(''),
            'rank' => $member->rank?->code ?? 'FA',
            'rank_name' => $member->rank?->name ?? 'Field Associate',
            'sponsor' => $member->sponsor?->name ?? 'None',
            'mentor' => $member->mentor?->name ?? 'Unassigned',
            'country' => $member->profile?->country ?? 'Global',
            'country_flag' => $this->countryFlag($member->profile?->country),
            'city' => $member->profile?->city ?? 'Not set',
            'timezone' => $member->profile?->timezone ?? 'Not set',
            'joined_at' => $member->joined_at?->format('M j, Y') ?? 'Not set',
            'status' => $member->is_active ? 'Active' : 'Inactive',
            'last_activity' => $member->last_login_at?->diffForHumans() ?? 'No activity yet',
            'upline_tree_url' => $uplineTreeUrl,
            'metrics' => $metrics,
            'progress' => $progress,
        ];
    }

    private function progressSummary(User $member): array
    {
        return $this->hierarchy->progressSummary($member);
    }

    private function countryFlag(?string $country): string
    {
        return match (strtolower((string) $country)) {
            'canada', 'ca' => 'CA',
            'united states', 'usa', 'us', 'u.s.', 'u.s.a.' => 'US',
            'philippines', 'ph' => 'PH',
            'global', '' => 'GL',
            default => strtoupper(str($country)->substr(0, 2)->value() ?: 'GL'),
        };
    }

    private function licensedCount(User $root): int
    {
        return $this->hierarchy->descendantsQuery($root)
            ->whereHas('profile', fn (Builder $query) => $query->whereNotNull('license_number'))
            ->count();
    }

    private function pendingLicensingCount(User $root): int
    {
        return $this->hierarchy->descendantsQuery($root)
            ->whereHas('profile', fn (Builder $query) => $query->whereNull('license_number'))
            ->count();
    }

    private function rankDistribution(User $root): array
    {
        return \DB::table('user_hierarchy_paths')
            ->join('users', 'users.id', '=', 'user_hierarchy_paths.descendant_id')
            ->leftJoin('ranks', 'users.rank_id', '=', 'ranks.id')
            ->where('user_hierarchy_paths.ancestor_id', $root->id)
            ->where('user_hierarchy_paths.depth', '>', 0)
            ->whereNull('users.deleted_at')
            ->selectRaw('COALESCE(ranks.code, "FA") as label, COUNT(users.id) as total')
            ->groupBy('label')
            ->orderByDesc('total')
            ->pluck('total', 'label')
            ->all();
    }

    private function countryDistribution(User $root): array
    {
        return \DB::table('user_hierarchy_paths')
            ->join('users', 'users.id', '=', 'user_hierarchy_paths.descendant_id')
            ->leftJoin('profiles', 'users.id', '=', 'profiles.user_id')
            ->where('user_hierarchy_paths.ancestor_id', $root->id)
            ->where('user_hierarchy_paths.depth', '>', 0)
            ->whereNull('users.deleted_at')
            ->selectRaw('COALESCE(profiles.country, "Global") as label, COUNT(users.id) as total')
            ->groupBy('label')
            ->orderByDesc('total')
            ->limit(6)
            ->pluck('total', 'label')
            ->all();
    }

    private function filterOptions(User $viewer): array
    {
        $countryQuery = $viewer->hasAnyRole(['super-admin', 'admin']) || $viewer->hasPermissionTo('view all teams')
            ? \DB::table('users')
            : \DB::table('user_hierarchy_paths')
                ->join('users', 'users.id', '=', 'user_hierarchy_paths.descendant_id')
                ->where('user_hierarchy_paths.ancestor_id', $viewer->id)
                ->when(
                    $viewer->hasPermissionTo('view direct downline') && ! $viewer->hasPermissionTo('view full downline'),
                    fn ($query) => $query->where('user_hierarchy_paths.depth', '<=', 1)
                );

        return [
            'ranks' => \DB::table('ranks')->whereNull('deleted_at')->where('is_active', true)->orderBy('sort_order')->get(['id', 'code', 'name']),
            'countries' => $countryQuery
                ->leftJoin('profiles', 'users.id', '=', 'profiles.user_id')
                ->whereNotNull('profiles.country')
                ->whereNull('users.deleted_at')
                ->distinct()
                ->orderBy('profiles.country')
                ->pluck('profiles.country'),
        ];
    }
}
