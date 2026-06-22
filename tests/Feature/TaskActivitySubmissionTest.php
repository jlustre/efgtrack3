<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserTask;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskActivitySubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_add_activity_comment_without_full_page_reload(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('member');

        $task = UserTask::query()->create([
            'assigned_to_user_id' => $user->id,
            'created_by_user_id' => $user->id,
            'title' => 'Follow up with prospect',
            'description' => 'Call back tomorrow.',
            'priority' => 'medium',
            'status' => 'to_do',
            'category' => 'Prospect Follow-Up',
            'related_module' => 'Prospects',
            'related_person' => 'Alex Rivera',
            'due_date' => now()->addDay()->toDateString(),
            'progress' => 0,
        ]);

        $this->actingAs($user)
            ->postJson(route('tasks.comments.store', $task), [
                'body' => 'Left voicemail and sent follow-up text.',
            ])
            ->assertCreated()
            ->assertJsonPath('comment.text', 'Left voicemail and sent follow-up text.')
            ->assertJsonPath('comment.author', $user->name);

        $this->assertDatabaseHas('user_task_comments', [
            'user_task_id' => $task->id,
            'user_id' => $user->id,
            'body' => 'Left voicemail and sent follow-up text.',
        ]);
    }
}
