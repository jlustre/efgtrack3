<?php

namespace Tests\Feature;

use App\Models\CalendarEvent;
use App\Models\CalendarCategory;
use App\Models\User;
use App\Models\UserCalendarPreference;
use Database\Seeders\CalendarModuleSeeder;
use Database\Seeders\RankSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\TeamSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalendarModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_open_calendar_views_with_seeded_events(): void
    {
        $this->seedCalendar();

        $user = User::where('email', 'calendar-member@efgtrack.com')->firstOrFail();

        foreach (['calendar.index', 'calendar.month', 'calendar.week', 'calendar.day', 'calendar.agenda', 'events.index'] as $route) {
            $this->actingAs($user)
                ->get(route($route))
                ->assertOk()
                ->assertSee('EFGTrack Calendar')
                ->assertSeeText('Calendar & Events');
        }
    }

    public function test_calendar_event_detail_is_visible_to_invited_attendee(): void
    {
        $this->seedCalendar();

        $user = User::where('email', 'calendar-member@efgtrack.com')->firstOrFail();
        $event = CalendarEvent::where('title', 'Monday Agency Leadership Huddle')->firstOrFail();

        $this->actingAs($user)
            ->get(route('calendar.events.show', $event))
            ->assertOk()
            ->assertSee('Monday Agency Leadership Huddle')
            ->assertSee('Attendees');
    }

    public function test_private_calendar_event_is_hidden_from_unrelated_member(): void
    {
        $this->seedCalendar();

        $unrelated = User::factory()->create();
        $unrelated->assignRole('member');

        $event = CalendarEvent::where('title', 'Prospect Appointment: Career Overview')->firstOrFail();

        $this->actingAs($unrelated)
            ->get(route('calendar.events.show', $event))
            ->assertForbidden();
    }

    public function test_calendar_export_downloads_visible_events(): void
    {
        $this->seedCalendar();

        $user = User::where('email', 'calendar-member@efgtrack.com')->firstOrFail();

        $this->actingAs($user)
            ->get(route('calendar.export'))
            ->assertOk()
            ->assertHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->assertSee('"Title","Type","Category"', false);
    }

    public function test_member_can_create_calendar_event_from_modal_form(): void
    {
        $this->seedCalendar();

        $user = User::where('email', 'calendar-member@efgtrack.com')->firstOrFail();
        $attendee = User::where('email', 'calendar-leader@efgtrack.com')->firstOrFail();

        $this->actingAs($user)
            ->get(route('calendar.index'))
            ->assertOk()
            ->assertSee('Add Calendar Event')
            ->assertSee('Repeat Schedule')
            ->assertSee('No typing needed')
            ->assertSee('Save Schedule');

        $response = $this->actingAs($user)
            ->post(route('calendar.store'), [
                'title' => 'New Client Strategy Session',
                'description' => 'Review prospect goals and prepare next steps.',
                'starts_at' => now()->addDays(2)->setTime(14, 0)->format('Y-m-d H:i:s'),
                'ends_at' => now()->addDays(2)->setTime(15, 0)->format('Y-m-d H:i:s'),
                'timezone' => 'America/Vancouver',
                'visibility' => 'shared_team',
                'status' => 'scheduled',
                'location' => 'Virtual',
                'is_recurring' => '1',
                'recurrence_frequency' => 'weekly',
                'recurrence_interval' => 2,
                'recurrence_weekdays' => ['MO', 'WE'],
                'recurrence_end_type' => 'after',
                'recurrence_ends_after_occurrences' => 8,
                'attendee_user_ids' => [$attendee->id],
                'reminder_minutes' => [15, 60],
                'reminder_channel' => 'in_app',
                'notes' => 'Created from modal test.',
                'return_to' => route('calendar.index'),
            ]);

        $response->assertRedirect(route('calendar.index'));

        $event = CalendarEvent::where('title', 'New Client Strategy Session')->firstOrFail();

        $this->assertSame($user->id, $event->organizer_id);
        $this->assertTrue($event->is_recurring);
        $this->assertSame('FREQ=WEEKLY;INTERVAL=2;BYDAY=MO,WE;COUNT=8', $event->recurrence_rule);
        $this->assertDatabaseHas('calendar_event_recurrences', [
            'calendar_event_id' => $event->id,
            'frequency' => 'weekly',
            'interval' => 2,
            'ends_after_occurrences' => 8,
        ]);
        $this->assertDatabaseHas('calendar_event_attendees', [
            'calendar_event_id' => $event->id,
            'user_id' => $attendee->id,
            'rsvp_status' => 'pending',
        ]);
        $this->assertDatabaseHas('calendar_event_reminders', [
            'calendar_event_id' => $event->id,
            'minutes_before' => 60,
            'channel' => 'in_app',
        ]);
    }

    public function test_my_calendars_checkboxes_filter_visible_events(): void
    {
        $this->seedCalendar();

        $user = User::where('email', 'calendar-member@efgtrack.com')->firstOrFail();
        $training = CalendarCategory::where('slug', 'training')->firstOrFail();

        $this->actingAs($user)
            ->get(route('calendar.index', [
                'calendars_filter' => 1,
                'category_ids' => [$training->id],
            ]))
            ->assertOk()
            ->assertSee('name="category_ids[]"', false)
            ->assertSee('New Associate Fast Start', false)
            ->assertDontSee('Licensing Milestone Review');

        $this->assertSame(
            [$training->id],
            UserCalendarPreference::where('user_id', $user->id)->firstOrFail()->visible_calendar_categories
        );

        $this->actingAs($user)
            ->get(route('calendar.index'))
            ->assertOk()
            ->assertSee('New Associate Fast Start', false)
            ->assertDontSee('Licensing Milestone Review');
    }

    public function test_new_user_gets_default_calendar_preferences_on_first_calendar_visit(): void
    {
        $this->seedCalendar();

        $user = User::factory()->create();
        $user->assignRole('member');

        $this->assertDatabaseMissing('user_calendar_preferences', ['user_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('calendar.index'))
            ->assertOk();

        $preference = UserCalendarPreference::where('user_id', $user->id)->firstOrFail();
        $defaultIds = CalendarCategory::where('is_active', true)->orderBy('sort_order')->pluck('id')->values()->all();

        $this->assertSame($defaultIds, $preference->visible_calendar_categories);
        $this->assertSame('month', $preference->default_view);
    }

    public function test_admin_can_customize_and_delete_calendar_category(): void
    {
        $this->seedCalendar();

        $admin = User::where('email', 'calendar-owner@efgtrack.com')->firstOrFail();
        $category = CalendarCategory::where('slug', 'team')->firstOrFail();

        $this->actingAs($admin)
            ->patch(route('calendar.categories.update', $category), [
                'name' => 'Agency Calendar',
                'color' => '#123ABC',
                'return_to' => route('calendar.index'),
            ])
            ->assertRedirect(route('calendar.index'));

        $this->assertDatabaseHas('calendar_categories', [
            'id' => $category->id,
            'name' => 'Agency Calendar',
            'color' => '#123ABC',
        ]);

        $this->actingAs($admin)
            ->get(route('calendar.index'))
            ->assertOk()
            ->assertSee('#123ABC', false);

        $this->actingAs($admin)
            ->delete(route('calendar.categories.destroy', $category), [
                'return_to' => route('calendar.index'),
            ])
            ->assertRedirect(route('calendar.index'));

        $this->assertSoftDeleted('calendar_categories', ['id' => $category->id]);
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
