<x-app-layout>
    <section
        x-data="taskManagement(@js($taskManagementPayload))"
        x-init="init()"
        wire:ignore
        class="space-y-6"
    >
        <div class="overflow-hidden rounded-lg border border-slate-200 bg-gradient-to-br from-white via-slate-50 to-[#F8F3E7] shadow-sm">
            @include('tasks.partials.management.header')
            @include('tasks.partials.management.stats')
        </div>

        <div class="flex flex-col gap-6 xl:flex-row xl:items-start">
            <div class="min-w-0 flex-1 space-y-5">
                @include('tasks.partials.management.tabs')
                @include('tasks.partials.management.filters')
                @include('tasks.partials.management.list')
                @include('tasks.partials.management.board')
                @include('tasks.partials.management.calendar')
                @include('tasks.partials.management.team')
                @include('tasks.partials.management.ai-panel')
            </div>

            @include('tasks.partials.management.sidebar')
        </div>

        @include('tasks.partials.management.mobile-drawer')
        @include('tasks.partials.management.new-task-modal')
    </section>
</x-app-layout>
