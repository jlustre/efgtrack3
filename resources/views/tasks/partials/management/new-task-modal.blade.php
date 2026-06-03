<div x-show="showNewTask" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4" @click.self="showNewTask = false" role="dialog" aria-modal="true">
    <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-lg border border-slate-200 bg-white p-6 shadow-xl">
        <div class="mb-5 flex items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-[#C8A24A]">New task</p>
                <h2 class="mt-1 text-lg font-semibold text-[#0B1F3A]">Create New Task</h2>
                <p class="mt-1 text-xs text-slate-500">Custom task creation will be available in a future release.</p>
            </div>
            <button type="button" @click="showNewTask = false" class="rounded-md border border-slate-200 p-2 text-slate-500 hover:border-[#C8A24A] hover:text-[#0B1F3A]">×</button>
        </div>
        <p class="mb-4 text-sm text-slate-600">Use the priority queue and fast actions to work your live EFGTrack assignments today.</p>
        <div class="flex justify-end gap-2">
            <button type="button" @click="showNewTask = false" class="rounded-md border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-600 hover:border-[#C8A24A]">Close</button>
            <a href="{{ route('dashboard') }}" class="rounded-md bg-[#C8A24A] px-5 py-2.5 text-sm font-semibold text-[#0B1F3A] hover:bg-[#D8B75F]">Go to Dashboard</a>
        </div>
    </div>
</div>
