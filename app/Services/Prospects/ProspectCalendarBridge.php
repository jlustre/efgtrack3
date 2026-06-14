<?php

namespace App\Services\Prospects;

use App\Models\CalendarEvent;
use App\Models\CalendarEventAttendee;
use App\Models\CalendarEventType;
use App\Models\CalendarCategory;
use App\Models\ProspectAppointment;
use Illuminate\Support\Str;

class ProspectCalendarBridge
{
    public function pushAppointment(ProspectAppointment $appt): CalendarEvent
    {
        $appt->loadMissing(['prospect', 'type', 'owner', 'assignedHelper']);

        $eventType = $this->resolveEventType($appt);
        $category = CalendarCategory::query()->where('slug', 'prospects')->first();
        [$location, $meetingLink] = $this->splitLocationOrLink($appt->location_or_link);

        $startsAt = $appt->scheduled_at;
        $endsAt = $startsAt?->copy()->addHour();

        $payload = [
            'calendar_event_type_id' => $eventType?->id,
            'calendar_category_id' => $category?->id ?? $eventType?->calendar_category_id,
            'organizer_id' => $appt->owner_id,
            'title' => $this->buildTitle($appt),
            'description' => $this->buildDescription($appt),
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'timezone' => $appt->timezone ?? config('app.timezone', 'America/Vancouver'),
            'location' => $location,
            'meeting_link' => $meetingLink,
            'visibility' => 'private',
            'status' => $this->mapAppointmentStatusToEventStatus($appt->status),
            'related_prospect_id' => $appt->prospect_id,
        ];

        if ($appt->calendar_event_id) {
            $event = CalendarEvent::query()->findOrFail($appt->calendar_event_id);
            $event->update($payload);
        } else {
            $event = CalendarEvent::create($payload);
            $appt->update(['calendar_event_id' => $event->id]);
        }

        $this->syncHelperAttendee($event, $appt);

        return $event->fresh(['type', 'category', 'attendees']);
    }

    public function pullChanges(CalendarEvent $event): void
    {
        if (! $event->related_prospect_id) {
            return;
        }

        $appointment = ProspectAppointment::query()
            ->where('calendar_event_id', $event->id)
            ->orWhere(function ($query) use ($event): void {
                $query->where('prospect_id', $event->related_prospect_id)
                    ->whereNull('calendar_event_id');
            })
            ->first();

        if (! $appointment) {
            return;
        }

        if (! $appointment->calendar_event_id) {
            $appointment->update(['calendar_event_id' => $event->id]);
        }

        $locationOrLink = $event->meeting_link ?: $event->location;

        $appointment->update([
            'scheduled_at' => $event->starts_at,
            'timezone' => $event->timezone,
            'location_or_link' => $locationOrLink,
            'status' => $this->mapEventStatusToAppointmentStatus($event->status),
        ]);
    }

    public function cancelAppointment(ProspectAppointment $appt): void
    {
        if (! $appt->calendar_event_id) {
            return;
        }

        $event = CalendarEvent::query()->find($appt->calendar_event_id);

        if (! $event) {
            return;
        }

        $event->update(['status' => 'cancelled']);
    }

    private function resolveEventType(ProspectAppointment $appt): ?CalendarEventType
    {
        $slug = match ($appt->type?->slug) {
            'financial-needs-analysis' => 'prospect-appointment',
            'product-presentation', 'career-overview' => 'prospect-appointment',
            default => 'prospect-appointment',
        };

        return CalendarEventType::query()->where('slug', $slug)->where('is_active', true)->first();
    }

    private function buildTitle(ProspectAppointment $appt): string
    {
        $prospectName = $appt->prospect?->displayName() ?? 'Prospect';
        $typeName = $appt->type?->name ?? 'Appointment';

        return "Prospect Appointment: {$typeName} — {$prospectName}";
    }

    private function buildDescription(ProspectAppointment $appt): ?string
    {
        $parts = array_filter([
            $appt->purpose,
            $appt->notes,
        ]);

        return $parts !== [] ? implode("\n\n", $parts) : null;
    }

    /**
     * @return array{0: ?string, 1: ?string}
     */
    private function splitLocationOrLink(?string $locationOrLink): array
    {
        if ($locationOrLink === null || trim($locationOrLink) === '') {
            return [null, null];
        }

        if (Str::startsWith(strtolower(trim($locationOrLink)), ['http://', 'https://', 'zoom.', 'www.'])) {
            return [null, trim($locationOrLink)];
        }

        return [trim($locationOrLink), null];
    }

    private function syncHelperAttendee(CalendarEvent $event, ProspectAppointment $appt): void
    {
        CalendarEventAttendee::query()
            ->where('calendar_event_id', $event->id)
            ->where('attendee_type', 'helper')
            ->delete();

        if (! $appt->assigned_helper_id) {
            return;
        }

        CalendarEventAttendee::create([
            'calendar_event_id' => $event->id,
            'user_id' => $appt->assigned_helper_id,
            'prospect_id' => $appt->prospect_id,
            'name' => $appt->assignedHelper?->name,
            'email' => $appt->assignedHelper?->email,
            'attendee_type' => 'helper',
            'rsvp_status' => 'accepted',
            'responded_at' => now(),
        ]);
    }

    private function mapAppointmentStatusToEventStatus(string $status): string
    {
        return match ($status) {
            'cancelled' => 'cancelled',
            'completed' => 'completed',
            'no_show' => 'cancelled',
            default => 'scheduled',
        };
    }

    private function mapEventStatusToAppointmentStatus(string $status): string
    {
        return match ($status) {
            'cancelled' => 'cancelled',
            'completed' => 'completed',
            default => 'scheduled',
        };
    }
}
