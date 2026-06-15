<?php

namespace App\Http\Controllers;

use App\Models\TaskSuggestion;
use App\Models\User;
use App\Models\UserTask;
use App\Support\ProfileLocationQuery;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function openTaskCountFor(User $user): int
    {
        return $this->confirmationTasksFor($user)->count()
            + $this->cfmAssignmentTasksFor($user)->count()
            + $this->emailTasksFor($user)->count()
            + $this->promotionTasksFor($user)->count()
            + UserTask::query()->openForUser($user)->count();
    }

    public function index(Request $request): View
    {
        $user = $request->user()->loadMissing(['profile', 'rank', 'team']);
        $confirmationTasks = $this->confirmationTasksFor($user);
        $cfmAssignmentTasks = $this->cfmAssignmentTasksFor($user);
        $emailTasks = $this->emailTasksFor($user);
        $promotionTasks = $this->promotionTasksFor($user);
        $today = now();

        $storedTasks = $this->storedTasksFor($user);

        $allTasks = collect()
            ->merge($confirmationTasks)
            ->merge($cfmAssignmentTasks)
            ->merge($emailTasks)
            ->merge($promotionTasks)
            ->sortBy([
                ['priority_order', 'asc'],
                ['created_at', 'asc'],
            ])
            ->values();

        $uiStoredTasks = $storedTasks
            ->map(fn (UserTask $task): array => $this->formatStoredTaskForUi($task))
            ->values();

        $stats = [
            'total' => $allTasks->count() + $storedTasks->whereNotIn('status', ['completed', 'cancelled'])->count(),
            'confirmations' => $confirmationTasks->count(),
            'cfm_assignments' => $cfmAssignmentTasks->count(),
            'emails' => $emailTasks->count(),
            'promotions' => $promotionTasks->count(),
            'high_priority' => $allTasks->where('priority', 'High')->count()
                + $storedTasks->whereIn('priority', ['high', 'urgent'])->whereNotIn('status', ['completed', 'cancelled'])->count(),
        ];

        $groupedTasks = [
            'Confirmations' => $confirmationTasks,
            'CFM Assignment' => $cfmAssignmentTasks,
            'Email Follow-Up' => $emailTasks,
            'Promotion Review' => $promotionTasks,
        ];

        $uiWorkflowTasks = $allTasks
            ->map(fn (array $task): array => $this->formatTaskForUi($task, $user))
            ->values();

        $uiTasks = $uiWorkflowTasks
            ->merge($uiStoredTasks)
            ->sortBy([
                fn (array $task) => match ($task['priority']) {
                    'Urgent' => 1,
                    'High' => 2,
                    'Medium' => 3,
                    default => 4,
                },
                'due',
            ])
            ->values();

        $overdueCount = $uiTasks->where('status', 'Overdue')->count()
            + $storedTasks->where('status', 'overdue')->count();

        $completedThisWeek = UserTask::query()
            ->where('assigned_to_user_id', $user->id)
            ->where('status', 'completed')
            ->where('completed_at', '>=', $today->copy()->startOfWeek())
            ->count();

        $aiSuggestions = TaskSuggestion::query()
            ->active()
            ->get()
            ->map(fn (TaskSuggestion $suggestion): array => [
                'icon' => $suggestion->icon,
                'text' => $suggestion->text,
            ])
            ->values()
            ->all();

        return view('tasks.index', [
            'user' => $user,
            'allTasks' => $allTasks,
            'groupedTasks' => $groupedTasks,
            'fastActions' => $this->fastActionsFor($user, $stats),
            'stats' => $stats,
            'todayLabel' => $today->format('M j, Y'),
            'taskManagementPayload' => [
                'currentUserName' => $user->name,
                'tasks' => $uiTasks,
                'teamMembers' => $this->teamMembersFromGroupedTasks($groupedTasks),
                'stats' => [
                    'total' => $stats['total'],
                    'dueToday' => $allTasks->filter(fn (array $task): bool => $this->isDueToday($task))->count()
                        + $storedTasks->filter(fn (UserTask $task): bool => $task->due_date?->isToday() ?? false)->count(),
                    'overdue' => $overdueCount,
                    'completedWeek' => $completedThisWeek,
                    'highPriority' => $stats['high_priority'],
                    'assignedToMe' => $storedTasks->where('assigned_to_user_id', $user->id)->whereNotIn('status', ['completed', 'cancelled'])->count()
                        + $allTasks->count(),
                ],
                'categories' => UserTask::CATEGORIES,
                'aiSuggestions' => $aiSuggestions,
                'calendarLabel' => $today->format('F Y'),
                'calendarFirstDay' => (int) $today->copy()->startOfMonth()->dayOfWeek,
                'calendarDaysInMonth' => (int) $today->daysInMonth,
                'calendarToday' => (int) $today->day,
                'calendarEvents' => $this->calendarEventsFor($uiTasks, $today),
            ],
        ]);
    }

    private function storedTasksFor(User $user): Collection
    {
        return UserTask::query()
            ->with([
                'assignee:id,name',
                'creator:id,name',
                'checklistItems',
                'comments.author:id,name',
            ])
            ->where(function ($query) use ($user): void {
                $query->where('assigned_to_user_id', $user->id);

                if ($user->team_id) {
                    $query->orWhereHas('assignee', fn ($assigneeQuery) => $assigneeQuery->where('team_id', $user->team_id));
                }
            })
            ->orderByRaw("case when status = 'overdue' then 0 when status = 'in_progress' then 1 when status = 'waiting' then 2 when status = 'to_do' then 3 else 4 end")
            ->orderBy('due_date')
            ->get();
    }

    private function formatStoredTaskForUi(UserTask $task): array
    {
        $assignee = $task->assignee;
        $assigneeName = $assignee?->name ?? 'Unassigned';
        $tone = match ($task->category) {
            'Licensing' => 'bg-amber-500',
            'CFM Mentorship' => 'bg-violet-500',
            'Prospect Follow-Up' => 'bg-sky-500',
            'Rank Advancement' => 'bg-purple-500',
            'Field Apprenticeship' => 'bg-blue-500',
            default => 'bg-emerald-500',
        };

        return [
            'id' => 'task-'.$task->id,
            'source' => 'database',
            'title' => $task->title,
            'desc' => (string) $task->description,
            'priority' => $task->displayPriority(),
            'status' => $task->displayStatus(),
            'category' => $task->category,
            'assignee' => $assigneeName,
            'initials' => $this->initialsFor($assigneeName),
            'avatarRing' => $this->avatarRingFor($tone),
            'due' => $task->due_date?->format('M j') ?? 'Soon',
            'dueDay' => $task->due_date?->day,
            'progress' => $task->progress,
            'module' => $task->related_module ?? 'Team',
            'related' => $task->related_person ?? '—',
            'createdBy' => $task->creator?->name ?? $assigneeName,
            'actionUrl' => null,
            'actionLabel' => 'Open',
            'reviewUrl' => null,
            'type' => 'Task',
            'memberEmail' => null,
            'meta' => $task->category,
            'age' => $task->created_at?->diffForHumans() ?? 'Recently',
            'checklistItems' => $task->checklistItems
                ->map(fn ($item): array => [
                    'id' => $item->id,
                    'done' => $item->is_done,
                    'text' => $item->text,
                ])
                ->values()
                ->all(),
            'comments' => $task->comments
                ->map(fn ($comment): array => [
                    'id' => $comment->id,
                    'author' => $comment->author?->name ?? 'Team member',
                    'initials' => $this->initialsFor($comment->author?->name ?? 'TM'),
                    'avatarRing' => $this->avatarRingFor('bg-slate-500'),
                    'time' => $comment->created_at?->diffForHumans() ?? 'Recently',
                    'text' => $comment->body,
                ])
                ->values()
                ->all(),
        ];
    }

    private function formatTaskForUi(array $task, User $user): array
    {
        $priority = match ($task['priority']) {
            'High' => 'High',
            default => 'Medium',
        };

        $createdAt = isset($task['created_at']) ? Carbon::parse($task['created_at']) : null;
        $isOverdue = $priority === 'High'
            && $createdAt
            && $createdAt->lt(now()->subDays(2));

        $status = $isOverdue ? 'Overdue' : 'To Do';

        if ($task['type'] === 'Confirmation') {
            $status = 'In Progress';
        } elseif ($task['type'] === 'Promotion Review') {
            $status = 'Waiting';
        }

        $category = $this->categoryForTask($task);
        $module = $this->moduleForTask($task);
        $initials = $this->initialsFor($user->name);
        $avatarRing = $this->avatarRingFor($task['tone'] ?? 'bg-slate-500');
        $dueDate = $createdAt?->copy()->addDays($priority === 'High' ? 2 : 5);

        return [
            'id' => $task['id'],
            'title' => $task['title'],
            'desc' => trim($task['subtitle'].' '.($task['description'] ?? '')),
            'priority' => $priority,
            'status' => $status,
            'category' => $category,
            'assignee' => $user->name,
            'initials' => $initials,
            'avatarRing' => $avatarRing,
            'due' => $dueDate?->format('M j') ?? 'Soon',
            'dueDay' => $dueDate?->day,
            'progress' => match ($status) {
                'In Progress' => 55,
                'Waiting' => 40,
                'Overdue' => 25,
                default => $priority === 'High' ? 35 : 15,
            },
            'module' => $module,
            'related' => $task['member_name'] ?? '—',
            'createdBy' => $user->name,
            'actionUrl' => $task['action_url'] ?? null,
            'actionLabel' => $task['action_label'] ?? 'Open',
            'reviewUrl' => $task['review_url'] ?? null,
            'type' => $task['type'],
            'memberEmail' => $task['member_email'] ?? null,
            'meta' => $task['meta'] ?? '',
            'age' => $task['age'] ?? '',
            'source' => 'workflow',
            'checklistItems' => [],
            'comments' => [],
        ];
    }

    private function categoryForTask(array $task): string
    {
        $meta = strtolower((string) ($task['meta'] ?? ''));

        return match ($task['type']) {
            'CFM Assignment' => 'CFM Mentorship',
            'Email Follow-Up' => 'Prospect Follow-Up',
            'Promotion Review' => 'Rank Advancement',
            'Confirmation' => match (true) {
                str_contains($meta, 'licensing') => 'Licensing',
                str_contains($meta, 'apprenticeship') => 'Field Apprenticeship',
                str_contains($meta, 'cfm') => 'CFM Mentorship',
                str_contains($meta, 'onboarding') => 'Training',
                default => 'Training',
            },
            default => 'Admin',
        };
    }

    private function moduleForTask(array $task): string
    {
        $meta = strtolower((string) ($task['meta'] ?? ''));

        return match ($task['type']) {
            'CFM Assignment' => 'Team',
            'Email Follow-Up' => 'Prospects',
            'Promotion Review' => 'Rank Advancement',
            'Confirmation' => match (true) {
                str_contains($meta, 'licensing') => 'Licensing',
                str_contains($meta, 'apprenticeship') => 'Training',
                default => 'Training',
            },
            default => 'Team',
        };
    }

    private function initialsFor(string $name): string
    {
        return collect(explode(' ', $name))
            ->filter()
            ->take(2)
            ->map(fn (string $part): string => str($part)->substr(0, 1)->upper()->toString())
            ->join('');
    }

    private function avatarRingFor(string $tone): string
    {
        return match ($tone) {
            'bg-emerald-500' => 'bg-emerald-500/20 text-emerald-300',
            'bg-amber-500' => 'bg-amber-500/20 text-amber-300',
            'bg-blue-500' => 'bg-blue-500/20 text-blue-300',
            'bg-violet-500' => 'bg-purple-500/20 text-purple-300',
            'bg-indigo-500' => 'bg-indigo-500/20 text-indigo-300',
            'bg-sky-500' => 'bg-sky-500/20 text-sky-300',
            'bg-purple-500' => 'bg-purple-500/20 text-purple-300',
            default => 'bg-[#0B1F3A] text-[#C8A24A]',
        };
    }

    private function isDueToday(array $task): bool
    {
        if (! isset($task['created_at'])) {
            return false;
        }

        $createdAt = Carbon::parse($task['created_at']);

        return $createdAt->isToday()
            || $createdAt->copy()->addDays($task['priority'] === 'High' ? 2 : 5)->isToday();
    }

    private function teamMembersFromGroupedTasks(array $groupedTasks): array
    {
        return collect($groupedTasks)
            ->map(function (Collection $tasks, string $name): array {
                $highPriority = $tasks->where('priority', 'High')->count();
                $total = $tasks->count();
                $completion = $total === 0
                    ? 0
                    : (int) round((($total - $highPriority) / $total) * 100);

                return [
                    'name' => $name,
                    'role' => 'Workstream queue',
                    'initials' => str($name)->explode(' ')->take(2)->map(fn (string $part) => str($part)->substr(0, 1)->upper())->join(''),
                    'avatarRing' => 'bg-[#0B1F3A] text-[#C8A24A]',
                    'taskCount' => $total,
                    'completion' => max($completion, 5),
                    'overdue' => $highPriority,
                ];
            })
            ->values()
            ->all();
    }

    private function calendarEventsFor(Collection $uiTasks, Carbon $today): array
    {
        $events = [];

        foreach ($uiTasks as $task) {
            if (empty($task['dueDay'])) {
                continue;
            }

            $day = (int) $task['dueDay'];

            if (! isset($events[$day])) {
                $events[$day] = [];
            }

            $events[$day][] = str($task['title'])->limit(22)->toString();
        }

        return $events;
    }

    private function confirmationTasksFor(User $user): Collection
    {
        return collect($this->checklistConfigs())
            ->flatMap(fn (array $config) => $this->pendingChecklistTasks($user, $config))
            ->sortBy('submitted_at')
            ->values();
    }

    private function pendingChecklistTasks(User $user, array $config): Collection
    {
        return DB::table($config['progress_table'])
            ->join('users', 'users.id', '=', $config['progress_table'].'.user_id')
            ->join($config['step_table'], $config['step_table'].'.id', '=', $config['progress_table'].'.'.$config['foreign_key'])
            ->leftJoin('profiles', 'profiles.user_id', '=', 'users.id')
<<<<<<< HEAD
            ->tap(fn ($query) => ProfileLocationQuery::joinCountry($query))
=======
            ->leftJoin('countries', 'countries.id', '=', 'profiles.country_id')
>>>>>>> 2ae99211b388cde4b56062c1cfbbc9ca81c523b0
            ->where($config['progress_table'].'.status', 'pending_confirmation')
            ->whereNull('users.deleted_at')
            ->whereNull($config['step_table'].'.deleted_at')
            ->where($config['step_table'].'.is_active', true)
            ->where('users.id', '!=', $user->id)
            ->select(
                $config['progress_table'].'.id',
                $config['progress_table'].'.submitted_at',
                'users.id as member_id',
                'users.name as member_name',
                'users.email as member_email',
                'users.sponsor_id',
                'users.mentor_id',
<<<<<<< HEAD
                ProfileLocationQuery::memberCountrySelect(),
=======
                'countries.name as member_country',
>>>>>>> 2ae99211b388cde4b56062c1cfbbc9ca81c523b0
                $config['step_table'].'.title',
                $config['step_table'].'.description',
                $config['step_table'].'.notified_parties'
            )
            ->orderBy($config['progress_table'].'.submitted_at')
            ->get()
            ->filter(fn (object $item) => $this->userCanConfirm($user, $item))
            ->map(function (object $item) use ($config): array {
                return [
                    'id' => $config['key'].'-'.$item->id,
                    'type' => 'Confirmation',
                    'title' => $item->title,
                    'subtitle' => $item->member_name.' submitted a '.$config['label'].' item.',
                    'member_name' => $item->member_name,
                    'member_email' => $item->member_email,
                    'meta' => $config['label'].' - Notify '.$item->notified_parties,
                    'description' => $item->description,
                    'priority' => $this->priorityFromAge($item->submitted_at),
                    'priority_order' => $this->priorityOrder($this->priorityFromAge($item->submitted_at)),
                    'created_at' => $item->submitted_at,
                    'age' => $item->submitted_at ? Carbon::parse($item->submitted_at)->diffForHumans() : 'Recently',
                    'action_label' => 'Open',
                    'action_url' => route($config['route']),
                    'review_url' => route($config['review_route'], $item->id),
                    'tone' => $config['tone'],
                ];
            })
            ->values();
    }

    private function cfmAssignmentTasksFor(User $user): Collection
    {
        if (! $user->hasAnyRole(['super-admin', 'admin', 'agency-owner'])) {
            return collect();
        }

        $query = User::query()
            ->with(['profile', 'sponsor'])
            ->whereHas('roles', fn ($query) => $query->whereIn('name', ['member', 'new-recruit', 'associate']))
            ->whereNull('mentor_id')
            ->where('id', '!=', $user->id)
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->latest('joined_at')
            ->latest('created_at')
            ->limit(10);

        if ($user->hasRole('agency-owner') && ! $user->hasAnyRole(['super-admin', 'admin'])) {
            $query->where(function ($query) use ($user): void {
                $query->where('sponsor_id', $user->id);

                if ($user->team_id) {
                    $query->orWhere('team_id', $user->team_id);
                }
            });
        }

        return $query->get()
            ->map(fn (User $member): array => [
                'id' => 'cfm-'.$member->id,
                'type' => 'CFM Assignment',
                'title' => 'Assign a CFM to '.$member->name,
                'subtitle' => 'New member needs Certified Field Mentor coverage.',
                'member_name' => $member->name,
                'member_email' => $member->email,
                'meta' => 'Sponsor '.($member->sponsor?->name ?? 'Not assigned').' - '.($member->profile?->country ?? 'Global'),
                'description' => 'Assign mentor support before apprenticeship activity begins.',
                'priority' => $member->joined_at && $member->joined_at->lt(now()->subDays(3)) ? 'High' : 'Normal',
                'priority_order' => $this->priorityOrder($member->joined_at && $member->joined_at->lt(now()->subDays(3)) ? 'High' : 'Normal'),
                'created_at' => $member->joined_at ?? $member->created_at,
                'age' => ($member->joined_at ?? $member->created_at)?->diffForHumans() ?? 'Recently',
                'action_label' => 'Assign',
                'action_url' => route('admin.users.edit', $member),
                'tone' => 'bg-indigo-500',
            ])
            ->values();
    }

    private function emailTasksFor(User $user): Collection
    {
        return DB::table('registration_invitations')
            ->where('sponsor_id', $user->id)
            ->whereNull('accepted_at')
            ->whereNull('accepted_by')
            ->whereNull('revoked_at')
            ->whereNull('deleted_at')
            ->whereNull('last_emailed_at')
            ->orderBy('created_at')
            ->limit(10)
            ->get()
            ->map(fn (object $invitation): array => [
                'id' => 'email-'.$invitation->id,
                'type' => 'Email Follow-Up',
                'title' => 'Send invitation email',
                'subtitle' => $invitation->email ? 'Invitation for '.$invitation->email.' has not been emailed yet.' : 'Invitation link has not been emailed yet.',
                'member_name' => $invitation->email ?: 'Prospective member',
                'member_email' => $invitation->email,
                'meta' => 'Invitation - Code '.$invitation->code,
                'description' => 'Send the invitation email from your profile invitation panel.',
                'priority' => $this->priorityFromAge($invitation->created_at),
                'priority_order' => $this->priorityOrder($this->priorityFromAge($invitation->created_at)),
                'created_at' => $invitation->created_at,
                'age' => $invitation->created_at ? Carbon::parse($invitation->created_at)->diffForHumans() : 'Recently',
                'action_label' => 'Open Profile',
                'action_url' => route('profile.edit'),
                'tone' => 'bg-sky-500',
            ])
            ->values();
    }

    private function promotionTasksFor(User $user): Collection
    {
        if (! $user->hasAnyRole(['super-admin', 'admin', 'agency-owner', 'team-leader'])) {
            return collect();
        }

        return DB::table('user_rank_progress')
            ->join('users', 'users.id', '=', 'user_rank_progress.user_id')
            ->join('rank_requirements', 'rank_requirements.id', '=', 'user_rank_progress.rank_requirement_id')
            ->join('ranks', 'ranks.id', '=', 'rank_requirements.rank_id')
            ->whereNull('users.deleted_at')
            ->whereNull('rank_requirements.deleted_at')
            ->whereNull('ranks.deleted_at')
            ->whereIn('user_rank_progress.status', ['pending', 'pending_confirmation', 'submitted', 'ready_for_review'])
            ->when($user->hasRole('team-leader') && ! $user->hasAnyRole(['super-admin', 'admin', 'agency-owner']), function ($query) use ($user): void {
                $query->where(function ($query) use ($user): void {
                    $query->where('users.sponsor_id', $user->id);

                    if ($user->team_id) {
                        $query->orWhere('users.team_id', $user->team_id);
                    }
                });
            })
            ->select(
                'user_rank_progress.id',
                'user_rank_progress.status',
                'user_rank_progress.created_at',
                'users.name as member_name',
                'users.email as member_email',
                'rank_requirements.title as requirement_title',
                'ranks.code as rank_code'
            )
            ->orderBy('user_rank_progress.created_at')
            ->limit(10)
            ->get()
            ->map(fn (object $item): array => [
                'id' => 'promotion-'.$item->id,
                'type' => 'Promotion Review',
                'title' => 'Review '.$item->rank_code.' advancement item',
                'subtitle' => $item->member_name.' has a rank requirement waiting.',
                'member_name' => $item->member_name,
                'member_email' => $item->member_email,
                'meta' => $item->rank_code.' - '.str($item->status)->replace('_', ' ')->title(),
                'description' => $item->requirement_title,
                'priority' => $this->priorityFromAge($item->created_at),
                'priority_order' => $this->priorityOrder($this->priorityFromAge($item->created_at)),
                'created_at' => $item->created_at,
                'age' => $item->created_at ? Carbon::parse($item->created_at)->diffForHumans() : 'Recently',
                'action_label' => 'Review',
                'action_url' => route('rank-advancement.index'),
                'tone' => 'bg-purple-500',
            ])
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

    private function checklistConfigs(): array
    {
        return [
            [
                'key' => 'onboarding',
                'label' => 'Onboarding',
                'step_table' => 'onboarding_steps',
                'progress_table' => 'user_onboarding_progress',
                'foreign_key' => 'onboarding_step_id',
                'route' => 'onboarding.index',
                'review_route' => 'onboarding.review',
                'tone' => 'bg-emerald-500',
            ],
            [
                'key' => 'licensing',
                'label' => 'Licensing',
                'step_table' => 'licensing_steps',
                'progress_table' => 'user_licensing_progress',
                'foreign_key' => 'licensing_step_id',
                'route' => 'licensing.index',
                'review_route' => 'licensing.review',
                'tone' => 'bg-amber-500',
            ],
            [
                'key' => 'apprenticeship',
                'label' => 'Field Apprenticeship',
                'step_table' => 'apprenticeship_steps',
                'progress_table' => 'user_apprenticeship_progress',
                'foreign_key' => 'apprenticeship_step_id',
                'route' => 'apprenticeship.index',
                'review_route' => 'apprenticeship.review',
                'tone' => 'bg-blue-500',
            ],
            [
                'key' => 'cfm-training',
                'label' => 'CFM Training',
                'step_table' => 'cfm_training_modules',
                'progress_table' => 'cfm_training_progress',
                'foreign_key' => 'cfm_training_module_id',
                'route' => 'cfm-training.index',
                'review_route' => 'cfm-training.review',
                'tone' => 'bg-violet-500',
            ],
        ];
    }

    private function fastActionsFor(User $user, array $stats): Collection
    {
        return collect($this->fastActionDefinitions())
            ->filter(function (array $action) use ($user): bool {
                if (isset($action['roles']) && ! $user->hasAnyRole($action['roles'])) {
                    return false;
                }

                if (isset($action['permissions']) && ! $user->hasAnyPermission($action['permissions'])) {
                    return false;
                }

                return true;
            })
            ->map(function (array $action) use ($stats): array {
                $countKey = $action['count_key'] ?? null;

                return [
                    ...$action,
                    'count' => $countKey ? ($stats[$countKey] ?? 0) : null,
                    'url' => route($action['route'], $action['params'] ?? []),
                ];
            })
            ->values();
    }

    private function fastActionDefinitions(): array
    {
        return [
            [
                'label' => 'Review onboarding',
                'description' => 'Confirm setup and sponsor-alignment items.',
                'route' => 'onboarding.index',
                'count_key' => 'confirmations',
                'icon' => 'check',
            ],
            [
                'label' => 'Review licensing',
                'description' => 'Open licensing milestones and pending approvals.',
                'route' => 'licensing.index',
                'count_key' => 'confirmations',
                'icon' => 'license',
            ],
            [
                'label' => 'Review FAP',
                'description' => 'Open apprenticeship progress and approvals.',
                'route' => 'apprenticeship.index',
                'count_key' => 'confirmations',
                'icon' => 'field',
            ],
            [
                'label' => 'Review CFM training',
                'description' => 'Open mentor training confirmations.',
                'route' => 'cfm-training.index',
                'count_key' => 'confirmations',
                'icon' => 'mentor',
            ],
            [
                'label' => 'Open invitations',
                'description' => 'Preview, send, or manage invitation emails.',
                'route' => 'profile.edit',
                'count_key' => 'emails',
                'icon' => 'mail',
            ],
            [
                'label' => 'Manage users',
                'description' => 'Assign CFM, team, rank, status, and sponsor.',
                'route' => 'admin.users.index',
                'count_key' => 'cfm_assignments',
                'icon' => 'users',
                'roles' => ['super-admin', 'admin', 'agency-owner'],
            ],
            [
                'label' => 'Rank advancement',
                'description' => 'Review rank progress and promotion tasks.',
                'route' => 'rank-advancement.index',
                'count_key' => 'promotions',
                'icon' => 'rank',
                'roles' => ['super-admin', 'admin', 'agency-owner', 'team-leader'],
            ],
        ];
    }

    private function priorityFromAge(?string $date): string
    {
        if (! $date) {
            return 'Normal';
        }

        return Carbon::parse($date)->lt(now()->subDays(2)) ? 'High' : 'Normal';
    }

    private function priorityOrder(string $priority): int
    {
        return match ($priority) {
            'High' => 1,
            'Normal' => 2,
            default => 3,
        };
    }
}
