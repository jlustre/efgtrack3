<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rank;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'role', 'rank_id', 'team_id', 'status', 'trashed']);

        $users = User::query()
            ->with(['rank', 'team', 'sponsor', 'roles'])
            ->when($filters['trashed'] ?? null, fn ($query, $trashed) => match ($trashed) {
                'with' => $query->withTrashed(),
                'only' => $query->onlyTrashed(),
                default => $query,
            })
            ->when($filters['search'] ?? null, function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($filters['role'] ?? null, fn ($query, string $role) => $query->role($role))
            ->when($filters['rank_id'] ?? null, fn ($query, string $rankId) => $query->where('rank_id', $rankId))
            ->when($filters['team_id'] ?? null, fn ($query, string $teamId) => $query->where('team_id', $teamId))
            ->when(($filters['status'] ?? null) !== null && ($filters['status'] ?? '') !== '', fn ($query) => $query->where('is_active', (bool) $filters['status']))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'roles' => Role::orderBy('name')->pluck('name'),
            'ranks' => Rank::orderBy('sort_order')->get(),
            'teams' => Team::orderBy('name')->get(),
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create', $this->formOptions());
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateUser($request);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'rank_id' => $validated['rank_id'] ?? null,
            'team_id' => $validated['team_id'] ?? null,
            'sponsor_id' => $validated['sponsor_id'] ?? null,
            'is_active' => (bool) $validated['is_active'],
            'joined_at' => $validated['joined_at'] ?? now(),
            'is_online' => false,
        ]);

        $user->syncRoles([$validated['role']]);

        return redirect()
            ->route('admin.users.edit', $user)
            ->with('status', 'user-created');
    }

    public function edit(User $user): View
    {
        $user->load(['rank', 'team', 'sponsor', 'roles', 'profile', 'messagingSuspendedBy']);

        return view('admin.users.edit', [
            'managedUser' => $user,
            ...$this->formOptions($user),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $this->validateUser($request, $user);

        if ($request->user()->is($user) && ! (bool) $validated['is_active']) {
            return back()
                ->withInput()
                ->withErrors(['is_active' => 'You cannot deactivate your own account.']);
        }

        $user->forceFill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'rank_id' => $validated['rank_id'] ?? null,
            'team_id' => $validated['team_id'] ?? null,
            'sponsor_id' => $validated['sponsor_id'] ?? null,
            'is_active' => (bool) $validated['is_active'],
            'joined_at' => $validated['joined_at'] ?? $user->joined_at,
        ])->save();

        if (! empty($validated['password'])) {
            $user->forceFill([
                'password' => Hash::make($validated['password']),
            ])->save();
        }

        $user->syncRoles([$validated['role']]);

        return redirect()
            ->route('admin.users.edit', $user)
            ->with('status', 'user-updated');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($request->user()->is($user)) {
            return back()->withErrors(['user' => 'You cannot delete your own account.']);
        }

        $user->forceFill([
            'is_active' => false,
            'is_online' => false,
        ])->save();

        $user->delete();

        return redirect()
            ->route('admin.users.index', ['trashed' => 'with'])
            ->with('status', 'user-deleted');
    }

    public function restore(int $user): RedirectResponse
    {
        $managedUser = User::withTrashed()->findOrFail($user);
        $managedUser->restore();

        return redirect()
            ->route('admin.users.edit', $managedUser)
            ->with('status', 'user-restored');
    }

    public function suspendMessaging(Request $request, User $user): RedirectResponse
    {
        if ($request->user()->is($user)) {
            return back()->withErrors(['messaging' => 'You cannot suspend your own messaging access.']);
        }

        if ($user->hasAnyRole(['super-admin', 'admin'])) {
            return back()->withErrors(['messaging' => 'Administrators cannot be suspended from messaging.']);
        }

        $validated = $request->validate([
            'messaging_suspension_reason' => ['required', 'string', 'max:1000'],
        ]);

        $user->forceFill([
            'messaging_suspended_at' => now(),
            'messaging_suspended_by' => $request->user()->id,
            'messaging_suspension_reason' => $validated['messaging_suspension_reason'],
        ])->save();

        return redirect()
            ->route('admin.users.edit', $user)
            ->with('status', 'messaging-suspended');
    }

    public function restoreMessaging(Request $request, User $user): RedirectResponse
    {
        $user->forceFill([
            'messaging_suspended_at' => null,
            'messaging_suspended_by' => null,
            'messaging_suspension_reason' => null,
        ])->save();

        return redirect()
            ->route('admin.users.edit', $user)
            ->with('status', 'messaging-restored');
    }

    private function validateUser(Request $request, ?User $user = null): array
    {
        $roleNames = Role::pluck('name')->all();

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user)],
            'password' => [$user ? 'nullable' : 'required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', Rule::in($roleNames)],
            'rank_id' => ['nullable', 'integer', Rule::exists('ranks', 'id')->whereNull('deleted_at')],
            'team_id' => ['nullable', 'integer', Rule::exists('teams', 'id')->whereNull('deleted_at')],
            'sponsor_id' => ['nullable', 'integer', Rule::exists('users', 'id')->whereNull('deleted_at'), Rule::notIn([$user?->id])],
            'is_active' => ['required', 'boolean'],
            'joined_at' => ['nullable', 'date'],
        ]);
    }

    private function formOptions(?User $user = null): array
    {
        return [
            'roles' => Role::orderBy('name')->pluck('name'),
            'ranks' => Rank::orderBy('sort_order')->get(),
            'teams' => Team::orderBy('name')->get(),
            'sponsors' => User::query()
                ->when($user, fn ($query) => $query->whereKeyNot($user->id))
                ->orderBy('name')
                ->get(['id', 'name', 'email']),
        ];
    }
}
