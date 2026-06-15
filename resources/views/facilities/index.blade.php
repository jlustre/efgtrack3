<x-app-layout>
    <section class="space-y-6">
        <div class="rounded-lg border border-slate-400 bg-gradient-to-br from-[#05070B] via-[#111827] to-[#2A2110] p-6 text-white shadow-sm">
            <p class="text-sm font-semibold uppercase tracking-wide text-[#C8A24A]">Company Resources</p>
            <h1 class="mt-2 text-2xl font-semibold">Facilities Websites</h1>
            <p class="mt-2 text-sm text-slate-300">Browse EFG facility locations, contact details, public websites, and leadership teams.</p>
        </div>

        <div class="rounded-lg border border-slate-400 bg-gradient-to-br from-white via-[#F8FAFC] to-[#FFF9EA] p-5 shadow-sm">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold text-[#0B1F3A]">Facilities</h2>
                    <p class="text-sm text-slate-600">{{ $facilities->count() }} active locations</p>
                </div>
            </div>

            <div class="overflow-x-auto rounded-lg border border-slate-300 bg-white/90">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="sticky top-0 bg-slate-50 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Facility Name</th>
                            <th class="px-4 py-3">Location</th>
                            <th class="px-4 py-3">Phone</th>
                            <th class="px-4 py-3">Domain</th>
                            <th class="px-4 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($facilities as $facility)
                            <tr x-data="{ viewOpen: false }">
                                <td class="px-4 py-3 font-medium text-[#0B1F3A]">{{ $facility->name }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $facility->location }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $facility->phone ?: 'N/A' }}</td>
                                <td class="px-4 py-3">
                                    <a
                                        href="{{ $facility->domainUrl() }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="font-medium text-[#0B1F3A] underline decoration-[#C8A24A]/60 underline-offset-2 hover:text-[#C8A24A]"
                                    >
                                        {{ $facility->domain }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <button
                                        type="button"
                                        x-on:click="viewOpen = true"
                                        class="inline-flex items-center rounded-lg border border-[#C8A24A] bg-[#C8A24A]/10 px-3 py-1.5 text-xs font-semibold text-[#0B1F3A] transition hover:bg-[#C8A24A]/20"
                                    >
                                        View
                                    </button>

                                    <div
                                        x-show="viewOpen"
                                        x-cloak
                                        x-transition.opacity
                                        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 p-4 text-left"
                                        role="dialog"
                                        aria-modal="true"
                                        aria-labelledby="facility-view-title-{{ $facility->id }}"
                                        x-on:keydown.escape.window="viewOpen = false"
                                    >
                                        <div class="absolute inset-0" x-on:click="viewOpen = false"></div>
                                        <div class="relative max-h-[94vh] w-full max-w-3xl overflow-y-auto rounded-lg bg-white shadow-2xl">
                                            <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-6 py-4">
                                                <div>
                                                    <p class="text-xs font-semibold uppercase tracking-wide text-[#C8A24A]">Facility Details</p>
                                                    <h2 id="facility-view-title-{{ $facility->id }}" class="mt-1 text-xl font-semibold text-[#0B1F3A]">{{ $facility->name }}</h2>
                                                </div>
                                                <button type="button" x-on:click="viewOpen = false" class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 text-slate-600 transition hover:bg-slate-200 hover:text-[#0B1F3A]" aria-label="Close facility details">
                                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                        <path d="M18 6 6 18" />
                                                        <path d="m6 6 12 12" />
                                                    </svg>
                                                </button>
                                            </div>

                                            <div class="space-y-6 px-6 py-5">
                                                <dl class="grid gap-4 md:grid-cols-2">
                                                    <div>
                                                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Location</dt>
                                                        <dd class="mt-2 rounded-md bg-slate-50 px-3 py-2 text-sm text-slate-800">{{ $facility->location }}</dd>
                                                    </div>
                                                    <div>
                                                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Phone</dt>
                                                        <dd class="mt-2 rounded-md bg-slate-50 px-3 py-2 text-sm text-slate-800">{{ $facility->phone ?: 'N/A' }}</dd>
                                                    </div>
                                                    <div>
                                                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Website</dt>
                                                        <dd class="mt-2 rounded-md bg-slate-50 px-3 py-2 text-sm text-slate-800">
                                                            <a
                                                                href="{{ $facility->domainUrl() }}"
                                                                target="_blank"
                                                                rel="noopener noreferrer"
                                                                class="font-medium text-[#0B1F3A] underline decoration-[#C8A24A]/60 underline-offset-2 hover:text-[#C8A24A]"
                                                            >
                                                                {{ $facility->domain }}
                                                            </a>
                                                        </dd>
                                                    </div>
                                                    <div>
                                                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Address</dt>
                                                        <dd class="mt-2 rounded-md bg-slate-50 px-3 py-2 text-sm text-slate-800">{{ $facility->formattedAddress() ?: 'N/A' }}</dd>
                                                    </div>
                                                    @if (filled($facility->description))
                                                        <div class="md:col-span-2">
                                                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Description</dt>
                                                            <dd class="mt-2 rounded-md bg-slate-50 px-3 py-2 text-sm leading-6 text-slate-800">{{ $facility->description }}</dd>
                                                        </div>
                                                    @endif
                                                </dl>

                                                <div>
                                                    <h3 class="text-sm font-semibold uppercase tracking-wide text-[#0B1F3A]">Leadership</h3>
                                                    @if (filled($facility->leadership))
                                                        <div class="mt-3 overflow-hidden rounded-lg border border-slate-200">
                                                            <table class="min-w-full divide-y divide-slate-200 text-sm">
                                                                <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                                                                    <tr>
                                                                        <th class="px-4 py-3">Name</th>
                                                                        <th class="px-4 py-3">Title</th>
                                                                        <th class="px-4 py-3">Email</th>
                                                                        <th class="px-4 py-3">Phone</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody class="divide-y divide-slate-100 bg-white">
                                                                    @foreach ($facility->leadership as $leader)
                                                                        <tr>
                                                                            <td class="px-4 py-3 font-medium text-[#0B1F3A]">{{ $leader['name'] ?? 'N/A' }}</td>
                                                                            <td class="px-4 py-3 text-slate-700">{{ $leader['title'] ?? 'N/A' }}</td>
                                                                            <td class="px-4 py-3 text-slate-700">{{ $leader['email'] ?? 'N/A' }}</td>
                                                                            <td class="px-4 py-3 text-slate-700">{{ $leader['phone'] ?? 'N/A' }}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    @else
                                                        <p class="mt-2 text-sm text-slate-600">No leadership contacts listed.</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-slate-500">No facilities are available right now.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</x-app-layout>
