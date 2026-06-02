<x-app-layout>
    <div class="mx-auto max-w-4xl space-y-5">
        <div>
            <p class="text-xs font-bold uppercase tracking-wide text-[#C8A24A]">Calendar Settings</p>
            <h1 class="text-2xl font-semibold text-[#0B1F3A]">My Calendar Preferences</h1>
            <p class="mt-1 text-sm text-slate-600">Starter preference scaffold for default view, timezone, visible calendars, and weekend display.</p>
        </div>

        <section class="rounded-lg border border-[#516070] bg-white p-5 shadow-sm">
            <div class="grid gap-4 md:grid-cols-2">
                <div class="rounded-lg border border-[#516070]/20 bg-[#F8FAFC] p-4">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Default View</p>
                    <p class="mt-1 text-lg font-semibold text-[#0B1F3A]">{{ str($preference->default_view)->headline() }}</p>
                </div>
                <div class="rounded-lg border border-[#516070]/20 bg-[#F8FAFC] p-4">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Timezone</p>
                    <p class="mt-1 text-lg font-semibold text-[#0B1F3A]">{{ $preference->timezone }}</p>
                </div>
                <div class="rounded-lg border border-[#516070]/20 bg-[#F8FAFC] p-4">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Weekends</p>
                    <p class="mt-1 text-lg font-semibold text-[#0B1F3A]">{{ $preference->show_weekends ? 'Visible' : 'Hidden' }}</p>
                </div>
                <div class="rounded-lg border border-[#516070]/20 bg-[#F8FAFC] p-4">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Active Calendars</p>
                    <p class="mt-1 text-lg font-semibold text-[#0B1F3A]">{{ $categories->count() }}</p>
                </div>
            </div>
        </section>

        <section class="rounded-lg border border-[#516070] bg-white p-5 shadow-sm">
            <h2 class="text-sm font-semibold text-[#0B1F3A]">Calendar Categories</h2>
            <div class="mt-4 grid gap-3 md:grid-cols-2">
                @foreach ($categories as $category)
                    <div class="flex items-center justify-between rounded-lg border border-[#516070]/20 bg-[#F8FAFC] p-3">
                        <div class="flex items-center gap-3">
                            <span class="h-3 w-3 rounded-full" style="background-color: {{ $category->color }}"></span>
                            <span class="text-sm font-semibold text-[#0B1F3A]">{{ $category->name }}</span>
                        </div>
                        <span class="text-xs font-semibold text-slate-500">{{ $category->is_active ? 'Active' : 'Inactive' }}</span>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
</x-app-layout>
