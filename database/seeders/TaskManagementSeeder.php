<?php

namespace Database\Seeders;

use App\Models\TaskSuggestion;
use App\Models\User;
use App\Models\UserTask;
use Illuminate\Database\Seeder;

class TaskManagementSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedSuggestions();

        $agencyOwner = User::where('email', 'agency-owner@efgtrack.com')->first();
        $cfm = User::where('email', 'cfm@efgtrack.com')->first();
        $sponsor = User::where('email', 'sponsor@efgtrack.com')->first();

        if (! $agencyOwner) {
            return;
        }

        $this->seedTask(
            $agencyOwner,
            $agencyOwner,
            [
                'title' => 'Follow up with David Kim on life insurance application',
                'description' => 'Review submitted docs and schedule call to finalize coverage options.',
                'priority' => 'urgent',
                'status' => 'overdue',
                'category' => 'Prospect Follow-Up',
                'related_module' => 'Prospects',
                'related_person' => 'David Kim',
                'due_date' => now()->subDay()->toDateString(),
                'progress' => 30,
            ],
            [
                ['text' => 'Review client file and previous notes', 'is_done' => true],
                ['text' => 'Prepare talking points for the call', 'is_done' => true],
                ['text' => 'Send pre-call email with agenda', 'is_done' => false],
                ['text' => 'Log outcome in CRM after call', 'is_done' => false],
            ],
            [
                ['author' => $agencyOwner, 'body' => 'Left a voicemail — will try again tomorrow morning.', 'created_at' => now()->subHours(2)],
                ['author' => $cfm, 'body' => 'Email sent with application status summary. Waiting on client response.', 'created_at' => now()->subDay()],
            ]
        );

        if ($cfm) {
            $this->seedTask(
                $agencyOwner,
                $cfm,
                [
                    'title' => 'Schedule CFM mentor review session with Marcus',
                    'description' => 'Monthly mentorship review — cover field performance and advancement criteria.',
                    'priority' => 'medium',
                    'status' => 'to_do',
                    'category' => 'CFM Mentorship',
                    'related_module' => 'Team',
                    'related_person' => 'Marcus Reed',
                    'due_date' => now()->addDays(8)->toDateString(),
                    'progress' => 0,
                ]
            );
        }

        if ($sponsor) {
            $this->seedTask(
                $agencyOwner,
                $sponsor,
                [
                    'title' => 'Send team meeting agenda for Thursday call',
                    'description' => 'Compile action items, highlight field metrics and recognition awards.',
                    'priority' => 'low',
                    'status' => 'to_do',
                    'category' => 'Team Meeting',
                    'related_module' => 'Team',
                    'due_date' => now()->addDays(4)->toDateString(),
                    'progress' => 10,
                ]
            );
        }

        $this->seedTask(
            $agencyOwner,
            $agencyOwner,
            [
                'title' => 'Review Q2 field apprenticeship progress reports',
                'description' => 'Compile scores and submit to regional director by end of week.',
                'priority' => 'high',
                'status' => 'waiting',
                'category' => 'Field Apprenticeship',
                'related_module' => 'Training',
                'related_person' => 'Apprentice Cohort',
                'due_date' => now()->addDays(10)->toDateString(),
                'progress' => 45,
            ]
        );

        $this->seedTask(
            $agencyOwner,
            $agencyOwner,
            [
                'title' => 'Prepare rank advancement documentation for Alex Torres',
                'description' => 'Gather production history, client letters and field supervisor sign-offs.',
                'priority' => 'medium',
                'status' => 'in_progress',
                'category' => 'Rank Advancement',
                'related_module' => 'Rank Advancement',
                'related_person' => 'Alex Torres',
                'due_date' => now()->addDays(12)->toDateString(),
                'progress' => 75,
            ]
        );

        $this->seedTask(
            $agencyOwner,
            $agencyOwner,
            [
                'title' => 'Complete Series 6 exam prep modules for Sandra',
                'description' => 'Assign exam prep material and set weekly check-in schedule.',
                'priority' => 'high',
                'status' => 'in_progress',
                'category' => 'Licensing',
                'related_module' => 'Licensing',
                'related_person' => 'Sandra M.',
                'due_date' => now()->addDays(6)->toDateString(),
                'progress' => 60,
            ]
        );

        $this->seedTask(
            $agencyOwner,
            $agencyOwner,
            [
                'title' => 'Upload updated training materials to resource library',
                'description' => 'New compliance slides from HQ — replace outdated versions.',
                'priority' => 'low',
                'status' => 'completed',
                'category' => 'Resource Review',
                'related_module' => 'Training',
                'related_person' => 'HQ Compliance',
                'due_date' => now()->subDays(2)->toDateString(),
                'progress' => 100,
                'completed_at' => now()->subDays(1),
            ]
        );

        $this->seedTask(
            $agencyOwner,
            $agencyOwner,
            [
                'title' => 'Onboard new prospect referral from regional network',
                'description' => 'Initial outreach call, gather info, enter into CRM pipeline.',
                'priority' => 'high',
                'status' => 'to_do',
                'category' => 'Prospect Follow-Up',
                'related_module' => 'Prospects',
                'related_person' => 'Referral #4421',
                'due_date' => now()->addDay()->toDateString(),
                'progress' => 0,
            ]
        );
    }

    private function seedSuggestions(): void
    {
        $suggestions = [
            ['icon' => '🔥', 'text' => 'Follow up with hot prospects approaching close window', 'sort_order' => 1],
            ['icon' => '📋', 'text' => 'Check overdue licensing steps for associates', 'sort_order' => 2],
            ['icon' => '🎯', 'text' => 'Schedule mentor review for associates nearing milestones', 'sort_order' => 3],
            ['icon' => '📈', 'text' => 'Review apprenticeship progress before month-end close', 'sort_order' => 4],
            ['icon' => '💼', 'text' => 'Invite inactive associates to upcoming training events', 'sort_order' => 5],
        ];

        foreach ($suggestions as $suggestion) {
            TaskSuggestion::updateOrCreate(
                ['text' => $suggestion['text']],
                [
                    'icon' => $suggestion['icon'],
                    'is_active' => true,
                    'sort_order' => $suggestion['sort_order'],
                ]
            );
        }
    }

    private function seedTask(
        User $creator,
        User $assignee,
        array $attributes,
        array $checklist = [],
        array $comments = []
    ): UserTask {
        $task = UserTask::updateOrCreate(
            [
                'assigned_to_user_id' => $assignee->id,
                'title' => $attributes['title'],
            ],
            [
                'created_by_user_id' => $creator->id,
                'description' => $attributes['description'] ?? null,
                'priority' => $attributes['priority'],
                'status' => $attributes['status'],
                'category' => $attributes['category'],
                'related_module' => $attributes['related_module'] ?? null,
                'related_person' => $attributes['related_person'] ?? null,
                'due_date' => $attributes['due_date'] ?? null,
                'progress' => $attributes['progress'] ?? 0,
                'reminder' => $attributes['reminder'] ?? null,
                'completed_at' => $attributes['completed_at'] ?? null,
            ]
        );

        $task->checklistItems()->delete();
        foreach ($checklist as $index => $item) {
            $task->checklistItems()->create([
                'text' => $item['text'],
                'is_done' => $item['is_done'] ?? false,
                'sort_order' => $index + 1,
            ]);
        }

        $task->comments()->delete();
        foreach ($comments as $comment) {
            $task->comments()->create([
                'user_id' => $comment['author']->id,
                'body' => $comment['body'],
                'created_at' => $comment['created_at'] ?? now(),
                'updated_at' => $comment['created_at'] ?? now(),
            ]);
        }

        return $task;
    }
}
