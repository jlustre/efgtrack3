<?php

namespace Tests\Feature;

use App\Models\CalendarScheduleBlock;
use App\Models\MentorAssignment;
use App\Models\User;
use App\Models\UserCalendarPreference;
use Database\Seeders\CalendarModuleSeeder;
use Database\Seeders\RankSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\TeamSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalendarScheduleBlocksTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_add_weekly_schedule_block_from_settings(): void
    {
        $this->seedCalendar();

        $user = User::where('email', 'calendar-member@efgtrack.com')->firstOrFail();

        $this->actingAs($user)
            ->post(route('calendar.schedule-blocks.store'), [
                'block_type' => 'work',
                'label' => 'Day Job',
                'weekday' => 1,
                'starts_at' => '09:00',
                'ends_at' => '17:00',
                'is_shared' => '1',
                'return_to' => route('calendar.settings'),
            ])
            ->assertRedirect(route('calendar.settings'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('calendar_schedule_blocks', [
            'user_id' => $user->id,
            'block_type' => 'work',
            'label' => 'Day Job',
            'weekday' => 1,
            'is_shared' => true,
        ]);
    }

    public function test_cfm_can_see_trainee_shared_schedule_blocks_on_calendar(): void
    {
        $this->seedCalendar();

        $cfm = User::where('email', 'calendar-cfm@efgtrack.com')->firstOrFail();
        $trainee = User::where('email', 'calendar-member@efgtrack.com')->firstOrFail();

        $trainee->update(['mentor_id' => $cfm->id]);

        MentorAssignment::query()->updateOrCreate(
            ['mentor_id' => $cfm->id, 'apprentice_id' => $trainee->id, 'status' => 'active'],
            ['assigned_by' => $cfm->id, 'started_at' => now()->subDays(5)]
        );

        UserCalendarPreference::query()->updateOrCreate(
            ['user_id' => $trainee->id],
            ['share_schedule_blocks_with_mentor' => true, 'default_view' => 'month', 'timezone' => 'PST']
        );

        CalendarScheduleBlock::create([
            'user_id' => $trainee->id,
            'block_type' => 'work',
            'label' => 'Trainee Work Shift',
            'weekday' => now()->dayOfWeekIso,
            'starts_at' => '08:00',
            'ends_at' => '16:00',
            'is_active' => true,
            'is_shared' => true,
        ]);

        $this->actingAs($cfm)
            ->get(route('calendar.week', ['date' => now()->toDateString()]))
            ->assertOk()
            ->assertSee('Trainee Work Shift', false)
            ->assertSee('Shared Availability', false);
    }

    public function test_trainee_cannot_see_private_schedule_blocks_from_other_users(): void
    {
        $this->seedCalendar();

        $cfm = User::where('email', 'calendar-cfm@efgtrack.com')->firstOrFail();
        $trainee = User::where('email', 'calendar-member@efgtrack.com')->firstOrFail();
        $other = User::where('email', 'calendar-leader@efgtrack.com')->firstOrFail();

        CalendarScheduleBlock::create([
            'user_id' => $other->id,
            'block_type' => 'personal',
            'label' => 'Secret Personal Time',
            'weekday' => now()->dayOfWeekIso,
            'starts_at' => '18:00',
            'ends_at' => '20:00',
            'is_active' => true,
            'is_shared' => true,
        ]);

        $this->actingAs($trainee)
            ->get(route('calendar.week', ['date' => now()->toDateString()]))
            ->assertOk()
            ->assertDontSee('Secret Personal Time');
    }

    public function test_member_can_toggle_schedule_sharing_with_mentor(): void
    {
        $this->seedCalendar();

        $user = User::where('email', 'calendar-member@efgtrack.com')->firstOrFail();

        $this->actingAs($user)
            ->patch(route('calendar.schedule-sharing.update'), [
                'return_to' => route('calendar.settings'),
            ])
            ->assertRedirect(route('calendar.settings'));

        $preference = UserCalendarPreference::where('user_id', $user->id)->firstOrFail();
        $this->assertFalse($preference->share_schedule_blocks_with_mentor);
    }

    private function seedCalendar(): void
    {
        $this->seed([
            RankSeeder::class,
            RolePermissionSeeder::class,
            TeamSeeder::class,
            CalendarModuleSeeder::class,
        ]);
    }
}
