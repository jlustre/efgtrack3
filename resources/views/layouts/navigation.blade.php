@php
    $linkClass = fn (bool $active) => 'efg-sidebar-top-link flex items-center rounded-md px-3 py-2 text-sm font-medium transition '.($active
        ? 'bg-[#C8A24A] text-[#0B1F3A]'
        : 'text-slate-200 hover:bg-white/10 hover:text-white');

    $childLinkClass = fn (bool $active) => 'efg-sidebar-child-link flex items-center rounded-md px-3 py-2 text-sm font-medium transition '.($active
        ? 'bg-white text-[#0B1F3A]'
        : 'text-slate-300 hover:bg-white/10 hover:text-white');

    $activeIsStrict = function (array $item): bool {
        $patterns = $item['active'] ?? [$item['route']];

        foreach ((array) $patterns as $pattern) {
            if (str_contains($pattern, '*')) {
                return false;
            }
        }

        return true;
    };

    $canSee = function (array $item): bool {
        if (! Route::has($item['route'])) {
            return false;
        }

        if (! auth()->check()) {
            return ! isset($item['roles']) && ! isset($item['permissions']);
        }

        if (isset($item['roles']) && ! auth()->user()->hasAnyRole($item['roles'])) {
            return false;
        }

        if (isset($item['permissions']) && ! auth()->user()->hasAnyPermission($item['permissions'])) {
            return false;
        }

        if (isset($item['access']) && ! auth()->user()->{$item['access']}()) {
            return false;
        }

        return true;
    };

    $isActive = function (array $item): bool {
        if (isset($item['active_resources'])) {
            return request()->routeIs('admin.management.*') && in_array(request()->route('resource'), $item['active_resources'], true);
        }

        if (isset($item['active_resource'])) {
            return request()->routeIs('admin.management.*') && request()->route('resource') === $item['active_resource'];
        }

        $patterns = $item['active'] ?? [$item['route']];

        foreach ((array) $patterns as $pattern) {
            if (request()->routeIs($pattern)) {
                return true;
            }
        }

        return false;
    };

    $user = auth()->user()?->loadMissing('profile');

    $topItems = [
        ['label' => 'My Dashboard', 'route' => 'dashboard'],
        ['label' => 'My Profile', 'route' => 'profile.edit', 'active' => ['profile.*']],
    ];

    $topItems = array_merge($topItems, [
        ['label' => 'My Messages', 'route' => 'messages.index', 'permissions' => ['view conversations'], 'active' => ['messages.*']],
        ['label' => 'Help & Support', 'route' => 'support.index', 'permissions' => ['submit support ticket'], 'active' => ['support.*']],
        ['label' => 'CFM Management', 'route' => 'team.cfms', 'access' => 'canAccessCfmManagement'],
        ['label' => 'CFM Portal', 'route' => 'cfm.portal', 'access' => 'canAccessCfmPortal'],
        ['label' => 'CFM Effectiveness', 'route' => 'cfm.effectiveness.index', 'permissions' => ['view CFM effectiveness'], 'active' => ['cfm.effectiveness.*']],
    ]);

    $groups = [
        [
            'label' => 'Trackers',
            'items' => [
                ['label' => 'My Tasks', 'route' => 'tasks.index'],
                ['label' => 'My Onboarding', 'route' => 'onboarding.index'],
                ['label' => 'Licensing Tracker', 'route' => 'licensing.index'],
                ['label' => 'Compliance & Renewals', 'route' => 'compliance.index'],
                ['label' => 'Field Apprenticeship', 'route' => 'apprenticeship.index'],
                ['label' => 'CFM Training', 'route' => 'cfm-training.index'],
                ['label' => 'Training Center', 'route' => 'training.index'],
                ['label' => 'Production Dashboard', 'route' => 'team.production', 'active' => ['team.production']],
                ['label' => 'Goals & Performance', 'route' => 'goals.index', 'permissions' => ['manage goals'], 'active' => ['goals.*']],
                ['label' => 'Assessments', 'route' => 'assessments.index'],
            ],
        ],
        [
            'label' => 'My Team',
            'items' => [
                ['label' => 'Downline Dashboard', 'route' => 'team.index', 'permissions' => ['view own team']],
                ['label' => 'Genealogy Tree', 'route' => 'team.tree', 'permissions' => ['view team tree']],
                ['label' => 'Hierarchy Table', 'route' => 'team.hierarchy', 'permissions' => ['view team tree']],
                ['label' => 'Org Chart', 'route' => 'team.org-chart', 'permissions' => ['view org chart']],
                ['label' => 'Team Table', 'route' => 'team.table', 'permissions' => ['view team table']],
                ['label' => 'My Directs', 'route' => 'team.directs', 'permissions' => ['view team']],
                ['label' => 'My Trainees', 'route' => 'team.trainees', 'permissions' => ['view team']],
                ['label' => 'All Downlines', 'route' => 'team.downlines', 'permissions' => ['view team']],
                ['label' => 'Prospect Management', 'route' => 'team.prospects', 'permissions' => ['manage prospects']],
                ['label' => 'Recruiting Pipeline', 'route' => 'team.recruiting.index', 'permissions' => ['manage prospects'], 'active' => ['team.recruiting.*']],
                ['label' => 'FNA Management', 'route' => 'team.fna.dashboard', 'permissions' => ['manage fna records']],
            ],
        ],
        [
            'label' => 'Communications',
            'items' => [
                ['label' => 'Communication Hub', 'route' => 'communications.index'],
                ['label' => 'Leadership Desk', 'route' => 'communications.leadership'],
                ['label' => 'Events', 'route' => 'events.index'],
                ['label' => 'Calendar', 'route' => 'calendar.index', 'active' => ['calendar.*']],
                ['label' => 'Mentor Scheduling', 'route' => 'bookings.dashboard', 'active' => ['bookings.*'], 'permissions' => ['view booking dashboard']],
                ['label' => 'Notifications', 'route' => 'notifications.index'],
                ['label' => 'Rank Advancement', 'route' => 'rank-advancement.index'],
                ['label' => 'Recognition', 'route' => 'communications.recognition'],
                ['label' => 'Campaigns', 'route' => 'communications.campaigns.index'],
            ],
        ],
        [
            'label' => 'Resources',
            'items' => [
                ['label' => 'Library Home', 'route' => 'resources.index', 'active' => ['resources.index']],
                ['label' => 'Documents', 'route' => 'resources.documents', 'active' => ['resources.documents']],
                ['label' => 'Videos', 'route' => 'resources.videos', 'active' => ['resources.videos']],
                ['label' => 'Recorded Webinars', 'route' => 'resources.recorded-webinars', 'active' => ['resources.recorded-webinars']],
                ['label' => 'Links', 'route' => 'resources.links', 'active' => ['resources.links', 'resources.zoom-links']],
            ],
        ],
        [
            'label' => 'Admin Management',
            'active' => ['admin.*'],
            'items' => [
                ['label' => 'Admin Dashboard', 'route' => 'admin.index', 'active' => ['admin.index'], 'roles' => ['super-admin', 'admin', 'agency-owner', 'team-leader', 'certified-field-mentor', 'trainer']],
                ['label' => 'Support Queue', 'route' => 'admin.support.index', 'permissions' => ['view all support tickets'], 'active' => ['admin.support.*']],
                ['label' => 'User Management', 'route' => 'admin.users.index', 'active' => ['admin.users.*'], 'roles' => ['super-admin', 'admin', 'agency-owner']],
                ['label' => 'Roles & Permissions', 'route' => 'admin.roles.index', 'permissions' => ['manage roles']],
                ['label' => 'Ranks', 'route' => 'admin.management.resource.index', 'params' => ['ranks'], 'active_resource' => 'ranks', 'roles' => ['super-admin', 'admin']],
                ['label' => 'Profile Completion Fields', 'route' => 'admin.management.resource.index', 'params' => ['profile-completion-fields'], 'active_resource' => 'profile-completion-fields', 'roles' => ['super-admin', 'admin']],
                ['label' => 'Teams', 'route' => 'admin.management.resource.index', 'params' => ['teams'], 'active_resource' => 'teams', 'roles' => ['super-admin', 'admin']],
                ['label' => 'Task Categories', 'route' => 'admin.management.resource.index', 'params' => ['task-categories'], 'active_resource' => 'task-categories', 'roles' => ['super-admin', 'admin']],
                ['label' => 'Tasks', 'route' => 'admin.management.resource.index', 'params' => ['tasks'], 'active_resource' => 'tasks', 'roles' => ['super-admin', 'admin']],
                ['label' => 'Task Assignments', 'route' => 'admin.management.resource.index', 'params' => ['task-users'], 'active_resource' => 'task-users', 'roles' => ['super-admin', 'admin']],
                ['label' => 'Checklists', 'route' => 'admin.checklists.index', 'active' => ['admin.checklists.*'], 'active_resources' => ['checklist-types', 'checklist-instructions', 'checklists'], 'roles' => ['super-admin', 'admin', 'agency-owner', 'team-leader', 'certified-field-mentor', 'trainer']],
                ['label' => 'Calendar Categories', 'route' => 'admin.management.resource.index', 'params' => ['calendar-categories'], 'active_resource' => 'calendar-categories', 'roles' => ['super-admin', 'admin', 'agency-owner']],
                ['label' => 'Calendar Event Types', 'route' => 'admin.management.resource.index', 'params' => ['calendar-event-types'], 'active_resource' => 'calendar-event-types', 'roles' => ['super-admin', 'admin', 'agency-owner']],
                ['label' => 'Calendar Events', 'route' => 'admin.management.resource.index', 'params' => ['calendar-events'], 'active_resource' => 'calendar-events', 'roles' => ['super-admin', 'admin', 'agency-owner']],
                ['label' => 'Booking Event Types', 'route' => 'admin.management.resource.index', 'params' => ['booking-event-types'], 'active_resource' => 'booking-event-types', 'roles' => ['super-admin', 'admin', 'agency-owner']],
                ['label' => 'Booking Links', 'route' => 'admin.management.resource.index', 'params' => ['booking-links'], 'active_resource' => 'booking-links', 'roles' => ['super-admin', 'admin', 'agency-owner']],
                ['label' => 'Bookings', 'route' => 'admin.management.resource.index', 'params' => ['bookings'], 'active_resource' => 'bookings', 'roles' => ['super-admin', 'admin', 'agency-owner']],
                ['label' => 'Email Templates', 'route' => 'admin.management.resource.index', 'params' => ['email-templates'], 'active_resource' => 'email-templates', 'roles' => ['super-admin', 'admin']],
                ['label' => 'Email Template Tokens', 'route' => 'admin.management.resource.index', 'params' => ['email-template-tokens'], 'active_resource' => 'email-template-tokens', 'roles' => ['super-admin', 'admin']],
                ['label' => 'Training Setup', 'route' => 'admin.training.index', 'active' => ['admin.training.*'], 'permissions' => ['manage training']],
                ['label' => 'CFM Certification', 'route' => 'admin.cfm.index', 'permissions' => ['manage CFM certification']],
                ['label' => 'All Setup Tables', 'route' => 'admin.management.index', 'active' => ['admin.management.index'], 'roles' => ['super-admin', 'admin']],
                ['label' => 'Admin Settings', 'route' => 'admin.settings', 'roles' => ['super-admin', 'admin', 'agency-owner']],
            ],
        ],
    ];

    $activeGroup = collect($groups)->first(function ($group) use ($canSee, $isActive) {
        $visibleItems = collect($group['items'])->filter(fn ($item) => $canSee($item))->values();

        return collect($group['active'] ?? [])->contains(fn ($pattern) => request()->routeIs($pattern))
            || $visibleItems->contains(fn ($item) => $isActive($item));
    })['label'] ?? null;

    $activeGroupKey = $activeGroup ? (string) str($activeGroup)->slug() : null;
@endphp

<nav
    id="efg-sidebar-navigation"
    class="space-y-2 px-3 py-5"
    data-server-active-group="{{ $activeGroupKey }}"
>
    <style>
        .efg-sidebar-group-panel[hidden] { display: none; }
        .efg-sidebar-group-button[data-open="true"] { background: rgb(255 255 255 / .1); color: #C8A24A; }
        .efg-sidebar-group-button[data-open="true"] .efg-sidebar-group-icon { background: rgb(200 162 74 / .2); color: #C8A24A; }
        .efg-sidebar-group-button[data-open="true"] svg { transform: rotate(180deg); }
    </style>

    @foreach ($topItems as $item)
        @continue(! $canSee($item))

        @php
            $itemKey = 'top-'.str($item['label'])->slug();
        @endphp

        <a
            href="{{ route($item['route'], $item['params'] ?? []) }}"
            wire:navigate
            class="{{ $linkClass($isActive($item)) }}"
            data-sidebar-link
            data-sidebar-group=""
            data-sidebar-item="{{ $itemKey }}"
            data-active-strict="{{ $activeIsStrict($item) ? 'true' : 'false' }}"
            @if ($isActive($item)) data-server-active-item="{{ $itemKey }}" @endif
        >
            {{ $item['label'] }}
        </a>
    @endforeach

    @foreach ($groups as $group)
        @php
            $visibleItems = collect($group['items'])->filter(fn ($item) => $canSee($item))->values();
            $groupActive = collect($group['active'] ?? [])->contains(fn ($pattern) => request()->routeIs($pattern))
                || $visibleItems->contains(fn ($item) => $isActive($item));
        @endphp

        @if ($visibleItems->isNotEmpty())
            @php
                $groupKey = (string) str($group['label'])->slug();
                $groupId = 'sidebar-group-'.$groupKey;
            @endphp

            <div class="rounded-md">
                <button
                    type="button"
                    id="{{ $groupId }}"
                    class="efg-sidebar-group-button group flex w-full cursor-pointer items-center justify-between rounded-md px-3 py-2 text-left text-xs font-bold uppercase tracking-wide text-slate-400 transition hover:bg-white/10 hover:text-white"
                    data-sidebar-group-button
                    data-sidebar-group="{{ $groupKey }}"
                    data-open="{{ $groupActive ? 'true' : 'false' }}"
                    aria-expanded="{{ $groupActive ? 'true' : 'false' }}"
                    aria-controls="{{ $groupId }}-panel"
                >
                    <span>{{ $group['label'] }}</span>
                    <span
                        class="efg-sidebar-group-icon flex h-6 w-6 items-center justify-center rounded-full bg-white/10 text-slate-300 transition group-hover:bg-white/15 group-hover:text-white"
                    >
                        <svg class="h-3.5 w-3.5 transition-transform duration-200" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
                        </svg>
                    </span>
                </button>

                <div
                    id="{{ $groupId }}-panel"
                    class="efg-sidebar-group-panel mt-1 space-y-1 border-l border-white/10 pl-3"
                    data-sidebar-group-panel
                    data-sidebar-group="{{ $groupKey }}"
                    @if (! $groupActive) hidden @endif
                >
                    @foreach ($visibleItems as $item)
                        @php
                            $itemKey = $groupKey.'-'.str($item['label'])->slug();
                        @endphp

                        <a
                            href="{{ route($item['route'], $item['params'] ?? []) }}"
                            wire:navigate
                            class="{{ $childLinkClass($isActive($item)) }}"
                            data-sidebar-link
                            data-sidebar-group="{{ $groupKey }}"
                            data-sidebar-item="{{ $itemKey }}"
                            data-active-strict="{{ $activeIsStrict($item) ? 'true' : 'false' }}"
                            @if ($isActive($item)) data-server-active-item="{{ $itemKey }}" @endif
                        >
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    @endforeach

    @auth
        <form method="POST" action="{{ route('logout') }}" class="pt-4">
            @csrf
            <button type="submit" class="w-full rounded-md px-3 py-2 text-left text-sm font-medium text-slate-300 transition hover:bg-white/10 hover:text-white">
                Log Out
            </button>
        </form>
    @endauth
</nav>
