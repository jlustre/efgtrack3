<x-app-layout>
    <x-bookings.partials.shell
        title="Mentor Booking Dashboard"
        subtitle="Manage CFM availability, booking links, pending requests, and mentor session outcomes connected to Field Apprenticeship."
    >
        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            @foreach ([['Upcoming', $upcomingBookings->count()], ['Pending Requests', $pendingRequests->count()], ['Event Types', $eventTypes->count()], ['Available Slots', 'Ready']] as [$label, $value])
                <div class="rounded-lg border border-[#516070] bg-[#FFF9EA] p-4 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ $label }}</p>
                    <p class="mt-2 text-2xl font-semibold text-[#0B1F3A]">{{ $value }}</p>
                </div>
            @endforeach
        </section>

        <section class="grid gap-4 xl:grid-cols-2">
            <div class="rounded-lg border border-[#516070] bg-white/90 p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-[#0B1F3A]">Upcoming Mentor Sessions</h2>
                    <a href="{{ route('bookings.my') }}" class="text-sm font-semibold text-[#8A6A1F]">View all</a>
                </div>
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-[#F8FAFC] text-left text-xs font-bold uppercase text-slate-500">
                            <tr><th class="px-3 py-2">Session</th><th class="px-3 py-2">When</th><th class="px-3 py-2">Status</th></tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($upcomingBookings as $booking)
                                <tr>
                                    <td class="px-3 py-2 font-semibold text-[#0B1F3A]">{{ $booking->eventType?->title ?? 'Mentor Session' }}</td>
                                    <td class="px-3 py-2 text-slate-600">{{ $booking->starts_at->format('M j, g:i A') }}</td>
                                    <td class="px-3 py-2 text-slate-600">{{ str($booking->status)->headline() }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="px-3 py-6 text-center text-slate-500">No upcoming booking sessions yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-lg border border-[#516070] bg-white/90 p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-[#0B1F3A]">Pending Requests</h2>
                    <a href="{{ route('bookings.requests') }}" class="text-sm font-semibold text-[#8A6A1F]">Review</a>
                </div>
                <div class="mt-4 space-y-3">
                    @forelse ($pendingRequests as $booking)
                        <div class="rounded-md border border-[#C8A24A]/50 bg-[#FFF9EA] px-3 py-2">
                            <div class="font-semibold text-[#0B1F3A]">{{ $booking->eventType?->title ?? 'Booking Request' }}</div>
                            <div class="text-xs text-slate-600">{{ $booking->starts_at->format('M j, g:i A') }} with {{ $booking->trainee?->name ?? 'External invitee' }}</div>
                        </div>
                    @empty
                        <div class="rounded-md border border-dashed border-[#516070]/40 bg-[#F8FAFC] px-4 py-6 text-sm text-slate-500">No approval requests waiting.</div>
                    @endforelse
                </div>
            </div>
        </section>
    </x-bookings.partials.shell>
</x-app-layout>
