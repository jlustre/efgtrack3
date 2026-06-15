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
                'timezone' => 'PST',
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
<<<<<<< HEAD
            ->assertSee('New Associate Fast Start')
=======
            ->assertSee('New Associate Fast Start', false)
>>>>>>> 2ae99211b388cde4b56062c1cfbbc9ca81c523b0
            ->assertDontSee('Licensing Milestone Review');

        $this->assertSame(
            [$training->id],
            UserCalendarPreference::where('user_id', $user->id)->firstOrFail()->visible_calendar_categories
        );

        $this->actingAs($user)
            ->get(route('calendar.index'))
            ->assertOk()
<<<<<<< HEAD
            ->assertSee('New Associate Fast Start')
=======
            ->assertSee('New Associate Fast Start', false)
>>>>>>> 2ae99211b388cde4b56062c1cfbbc9ca81c523b0
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
        $defaultIds = CalendarCategory::query()
            ->where('is_active', true)
            ->whereNull('user_id')
            ->orderBy('sort_order')
            ->pluck('id')
            ->values()
            ->all();

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

    public function test_member_can_create_personal_calendar_category(): void
    {
        $this->seedCalendar();

        $user = User::where('email', 'calendar-member@efgtrack.com')->firstOrFail();

        $this->actingAs($user)
            ->post(route('calendar.categories.store'), [
                'name' => 'Client Follow-ups',
                'color' => '#AABBCC',
                'return_to' => route('calendar.index'),
            ])
            ->assertRedirect(route('calendar.index'));

        $category = CalendarCategory::where('name', 'Client Follow-ups')->firstOrFail();

        $this->assertSame($user->id, $category->user_id);
        $this->assertFalse($category->is_public);
        $this->assertContains($category->id, UserCalendarPreference::where('user_id', $user->id)->firstOrFail()->visible_calendar_categories);

        $this->actingAs($user)
            ->get(route('calendar.index'))
            ->assertOk()
            ->assertSee('Client Follow-ups')
            ->assertSee('Private');
    }

    public function test_private_calendar_category_is_hidden_from_other_users(): void
    {
        $this->seedCalendar();

        $owner = User::where('email', 'calendar-member@efgtrack.com')->firstOrFail();
        $other = User::where('email', 'calendar-leader@efgtrack.com')->firstOrFail();

        $category = CalendarCategory::create([
            'user_id' => $owner->id,
            'name' => 'Secret Pipeline',
            'slug' => 'u'.$owner->id.'-secret-pipeline',
            'color' => '#112233',
            'icon' => 'calendar',
            'sort_order' => 910,
            'is_active' => true,
            'is_public' => false,
        ]);

        $this->actingAs($other)
            ->get(route('calendar.index'))
            ->assertOk()
            ->assertDontSee('Secret Pipeline');

        $this->assertFalse(
            CalendarCategory::query()->visibleTo($other)->whereKey($category->id)->exists()
        );
    }

    public function test_public_calendar_category_is_visible_to_other_users(): void
    {
        $this->seedCalendar();

        $owner = User::where('email', 'calendar-member@efgtrack.com')->firstOrFail();
        $other = User::where('email', 'calendar-leader@efgtrack.com')->firstOrFail();

        CalendarCategory::create([
            'user_id' => $owner->id,
            'name' => 'Shared Coaching',
            'slug' => 'u'.$owner->id.'-shared-coaching',
            'color' => '#445566',
            'icon' => 'calendar',
            'sort_order' => 920,
            'is_active' => true,
            'is_public' => true,
        ]);

        $this->actingAs($other)
            ->get(route('calendar.index'))
            ->assertOk()
            ->assertSee('Shared Coaching')
            ->assertSee('Public');
    }

    public function test_member_can_update_and_delete_own_calendar_category(): void
    {
        $this->seedCalendar();

        $user = User::where('email', 'calendar-member@efgtrack.com')->firstOrFail();

        $category = CalendarCategory::create([
            'user_id' => $user->id,
            'name' => 'My Tasks',
            'slug' => 'u'.$user->id.'-my-tasks',
            'color' => '#778899',
            'icon' => 'calendar',
            'sort_order' => 930,
            'is_active' => true,
            'is_public' => false,
        ]);

        $this->actingAs($user)
            ->patch(route('calendar.categories.update', $category), [
                'name' => 'My Priorities',
                'color' => '#99AABB',
                'is_public' => '1',
                'return_to' => route('calendar.index'),
            ])
            ->assertRedirect(route('calendar.index'));

        $category->refresh();
        $this->assertSame('My Priorities', $category->name);
        $this->assertTrue($category->is_public);

        $this->actingAs($user)
            ->delete(route('calendar.categories.destroy', $category), [
                'return_to' => route('calendar.index'),
            ])
            ->assertRedirect(route('calendar.index'));

        $this->assertSoftDeleted('calendar_categories', ['id' => $category->id]);
    }

    public function test_member_cannot_manage_another_users_calendar_category(): void
    {
        $this->seedCalendar();

        $owner = User::where('email', 'calendar-member@efgtrack.com')->firstOrFail();
        $other = User::where('email', 'calendar-leader@efgtrack.com')->firstOrFail();

        $category = CalendarCategory::create([
            'user_id' => $owner->id,
            'name' => 'Owner Only',
            'slug' => 'u'.$owner->id.'-owner-only',
            'color' => '#AABBCC',
            'icon' => 'calendar',
            'sort_order' => 940,
            'is_active' => true,
            'is_public' => false,
        ]);

        $this->actingAs($other)
            ->patch(route('calendar.categories.update', $category), [
                'name' => 'Hijacked',
                'color' => '#000000',
                'return_to' => route('calendar.index'),
            ])
            ->assertForbidden();

        $this->actingAs($other)
            ->delete(route('calendar.categories.destroy', $category), [
                'return_to' => route('calendar.index'),
            ])
            ->assertForbidden();
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
