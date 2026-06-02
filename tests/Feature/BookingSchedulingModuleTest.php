<?php

namespace Tests\Feature;

use App\Models\AvailabilitySchedule;
use App\Models\Booking;
use App\Models\BookingEventType;
use App\Models\BookingLink;
use App\Models\User;
use Database\Seeders\BookingSchedulingSeeder;
use Database\Seeders\CalendarModuleSeeder;
use Database\Seeders\RankSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\TeamSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingSchedulingModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_scheduling_seeder_creates_cfm_foundation_data(): void
    {
        $this->seedScheduling();

        $this->assertGreaterThanOrEqual(8, BookingEventType::count());
        $this->assertGreaterThanOrEqual(1, AvailabilitySchedule::count());
        $this->assertGreaterThanOrEqual(8, BookingLink::count());
        $this->assertDatabaseHas('bookings', ['status' => 'confirmed']);
        $this->assertDatabaseHas('bookings', ['status' => 'pending_approval']);
        $this->assertDatabaseHas('calendar_events', ['title' => 'Weekly Progress Review - Booking Apprentice Trainee']);
    }

    public function test_cfm_can_open_booking_workspace_pages(): void
    {
        $this->seedScheduling();

        $cfm = User::where('email', 'booking-cfm@efgtrack.com')->firstOrFail();

        foreach ([
            'bookings.dashboard',
            'bookings.availability',
            'bookings.event-types',
            'bookings.links',
            'bookings.requests',
            'bookings.my',
            'bookings.calendar',
            'bookings.settings',
        ] as $route) {
            $this->actingAs($cfm)
                ->get(route($route))
                ->assertOk()
                ->assertSee('CFM Mentor Scheduling');
        }
    }

    public function test_trainee_can_view_own_bookings_but_not_cfm_availability_manager(): void
    {
        $this->seedScheduling();

        $trainee = User::where('email', 'booking-trainee@efgtrack.com')->firstOrFail();

        $this->actingAs($trainee)
            ->get(route('bookings.my'))
            ->assertOk()
            ->assertSee('My Bookings');

        $this->actingAs($trainee)
            ->get(route('bookings.availability'))
            ->assertForbidden();
    }

    public function test_public_booking_invite_page_renders_seeded_event_type(): void
    {
        $this->seedScheduling();

        $link = BookingLink::where('link_type', 'apprentice')->firstOrFail();

        $this->get(route('bookings.invite', $link->token))
            ->assertOk()
            ->assertSee('Book a Session')
            ->assertSee('Field Apprenticeship Session');
    }

    public function test_admin_management_has_booking_resources(): void
    {
        $this->seedScheduling();

        $admin = User::where('email', 'calendar-owner@efgtrack.com')->firstOrFail();
        $admin->assignRole('admin');

        foreach (['booking-event-types', 'booking-links', 'bookings'] as $resource) {
            $this->actingAs($admin)
                ->get(route('admin.management.resource.index', $resource))
                ->assertOk();
        }
    }

    private function seedScheduling(): void
    {
        $this->seed([
            RankSeeder::class,
            RolePermissionSeeder::class,
            TeamSeeder::class,
            CalendarModuleSeeder::class,
            BookingSchedulingSeeder::class,
        ]);
    }
}
