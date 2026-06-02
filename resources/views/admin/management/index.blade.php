<x-app-layout>
    <section class="space-y-6">
        <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-semibold uppercase tracking-wide text-[#C8A24A]">Admin Management</p>
            <h1 class="mt-2 text-2xl font-semibold text-[#0B1F3A]">Operations Table Management</h1>
            <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-600">
                Manage the key setup tables that power onboarding, licensing, training, recognition, communication, events, teams, and rank advancement.
            </p>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($resources as $key => $resource)
                <a
                    href="{{ route('admin.management.resource.index', $key) }}"
                    class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm transition hover:border-[#C8A24A] hover:shadow-md"
                >
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-base font-semibold text-[#0B1F3A]">{{ $resource['label'] }}</h2>
                            <p class="mt-2 text-sm leading-6 text-slate-600">{{ $resource['description'] }}</p>
                        </div>
                        <span class="rounded-full bg-[#0B1F3A] px-3 py-1 text-xs font-semibold text-white">Manage</span>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
</x-app-layout>
