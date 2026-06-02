<x-app-layout>
    <section class="space-y-6">
        <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-semibold uppercase tracking-wide text-[#C8A24A]">Portal Search</p>
            <h1 class="mt-2 text-2xl font-semibold text-[#0B1F3A]">Search Results</h1>
            <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-600">
                Search is ready for global results across members, teams, training, resources, announcements, and events.
            </p>

            @if (request('q'))
                <div class="mt-5 rounded-md border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">
                    Showing scaffold results for <span class="font-semibold text-[#0B1F3A]">"{{ request('q') }}"</span>.
                </div>
            @endif
        </div>
    </section>
</x-app-layout>
