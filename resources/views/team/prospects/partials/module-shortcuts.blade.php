<div class="grid gap-6 xl:grid-cols-[1fr_22rem]">
    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-5 py-4">
            <h2 class="text-lg font-semibold text-[#0B1F3A]">Module shortcuts</h2>
            <p class="mt-1 text-sm text-slate-600">Jump to specialized CRM tools</p>
        </div>
        <div class="grid gap-3 p-5 sm:grid-cols-2">
            @foreach ([
                ['Recruiting Pipeline', 'Dedicated associate recruiting kanban, invitations, and recruit journey — separate from sales CRM.', route('team.recruiting.index')],
                ['Add Prospect', 'Create prospects with source, tags, interests, and privacy defaults.', route('team.prospects.create')],
                ['Pipeline Board', 'Kanban board for stage, priority, interest, and conversion flow.', route('team.prospects.pipeline')],
                ['Follow-Up Center', 'Daily follow-ups, overdue tasks, and completion tracking.', route('team.prospects.follow-ups')],
                ['Appointment Calendar', 'Scheduled calls, reminders, no-shows, and reschedules.', route('team.prospects.appointments')],
                ['Analytics & Goals', 'Funnel conversion charts and period goal tracking.', route('team.prospects.analytics')],
                ['Access Manager', 'Grant, expire, revoke, and audit sharing permissions.', route('team.prospects.access-manager')],
                ['AI Coach', 'Recommendations for stalled leads and overdue follow-ups.', route('team.prospects.ai-coach')],
                ['Prospect Import', 'CSV preview, duplicate detection, and merge workflows.', route('team.prospects.import')],
            ] as [$title, $description, $url])
                <a href="{{ $url }}" class="rounded-lg border border-slate-200 bg-slate-50/80 p-4 transition hover:border-[#C8A24A]/50 hover:bg-[#FFF9EA]">
                    <h3 class="font-semibold text-[#0B1F3A]">{{ $title }}</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600">{{ $description }}</p>
                </a>
            @endforeach
        </div>
    </section>

    <aside class="space-y-5">
        <livewire:prospects.prospect-goals-panel :compact="true" />

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-base font-semibold text-[#0B1F3A]">Privacy Rules</h2>
            <div class="mt-3 space-y-2 text-sm leading-6 text-slate-600">
                <p>Prospects are private by default and visible only to the owner.</p>
                <p>Shared users can access only explicitly shared records while access is active.</p>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-gradient-to-br from-[#0B1F3A] to-[#132F55] p-5 text-white shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-[#C8A24A]">Test data</p>
            <p class="mt-2 text-sm leading-6 text-slate-200">Seed the dashboard with curated QA prospects:</p>
            <code class="mt-3 block rounded bg-black/20 px-2 py-1 text-xs text-[#FFF9EA]">php artisan db:seed --class=ProspectDashboardTestSeeder</code>
            <p class="mt-2 text-xs text-slate-300">Login: prospects@efgtrack.com / Password123</p>
        </div>
    </aside>
</div>
