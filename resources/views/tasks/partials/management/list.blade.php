<div x-show="['list','my','completed'].includes(activeView)" x-cloak class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
    <div class="hidden border-b border-slate-200 bg-slate-50 px-4 py-2 text-[11px] font-semibold uppercase tracking-wider text-slate-500 lg:grid lg:grid-cols-12 lg:gap-3">
        <span class="lg:col-span-4">Task</span>
        <span class="lg:col-span-1">Priority</span>
        <span class="lg:col-span-1">Status</span>
        <span class="lg:col-span-2">Category</span>
        <span class="lg:col-span-1">Due</span>
        <span class="lg:col-span-1">Module</span>
        <span class="lg:col-span-1">Assign</span>
        <span class="lg:col-span-1 text-right">Actions</span>
    </div>

    <template x-if="filteredTasks.length === 0">
        <div class="px-6 py-12 text-center">
            <p class="text-sm font-semibold text-[#0B1F3A]">No open tasks</p>
            <p class="mt-2 text-xs text-slate-500">You are clear right now.</p>
        </div>
    </template>

    <template x-for="task in filteredTasks" :key="task.id">
        <article
            @click="selectTask(task)"
            :class="selectedTask?.id === task.id ? 'border-l-4 border-l-[#C8A24A] bg-[#FFF9EA]/60' : 'border-l-4 border-l-transparent'"
            class="cursor-pointer border-b border-slate-100 px-4 py-4 transition last:border-b-0 hover:bg-slate-50 lg:grid lg:grid-cols-12 lg:items-center lg:gap-3 lg:py-3.5"
        >
            <div class="lg:col-span-4">
                <h3 class="text-sm font-semibold text-[#0B1F3A]" x-text="task.title"></h3>
                <p class="mt-1 line-clamp-2 text-xs text-slate-600" x-text="task.desc"></p>
                <div class="mt-2 flex flex-wrap items-center gap-2 lg:hidden">
                    <span :class="priorityClass(task.priority)" class="rounded-full px-2 py-0.5 text-[10px] font-semibold" x-text="task.priority"></span>
                    <span :class="statusClass(task.status)" class="rounded-full px-2 py-0.5 text-[10px] font-semibold" x-text="task.status"></span>
                    <span class="rounded-full bg-[#C8A24A]/15 px-2 py-0.5 text-[10px] font-bold text-[#8A6A1F]" x-text="task.category"></span>
                </div>
                <div class="mt-2 h-1.5 w-full max-w-[140px] overflow-hidden rounded-full bg-slate-100">
                    <div class="h-full rounded-full bg-[#C8A24A]" :style="`width:${task.progress}%`"></div>
                </div>
            </div>
            <div class="mt-3 hidden lg:col-span-1 lg:block"><span :class="priorityClass(task.priority)" class="rounded-full px-2 py-0.5 text-[10px] font-semibold" x-text="task.priority"></span></div>
            <div class="hidden lg:col-span-1 lg:block"><span :class="statusClass(task.status)" class="rounded-full px-2 py-0.5 text-[10px] font-semibold" x-text="task.status"></span></div>
            <div class="hidden lg:col-span-2 lg:block"><span class="rounded-full bg-[#C8A24A]/15 px-2 py-0.5 text-[10px] font-bold text-[#8A6A1F]" x-text="task.category"></span></div>
            <div class="hidden text-xs lg:col-span-1 lg:block" :class="task.status === 'Overdue' ? 'font-semibold text-red-700' : 'text-slate-600'" x-text="task.due"></div>
            <div class="hidden text-xs text-slate-600 lg:col-span-1 lg:block" x-text="task.module"></div>
            <div class="hidden lg:col-span-1 lg:flex">
                <div class="flex h-8 w-8 items-center justify-center rounded-full text-[11px] font-semibold" :class="task.avatarRing" x-text="task.initials"></div>
            </div>
            <div class="mt-3 flex items-center justify-end gap-2 lg:col-span-1 lg:mt-0" @click.stop>
                <button type="button" @click="selectTask(task)" class="rounded-md border border-slate-200 px-2 py-1 text-xs font-medium text-slate-600 hover:border-[#C8A24A] hover:text-[#0B1F3A]">View</button>
                <button type="button" @click="openTask(task)" class="rounded-md bg-[#C8A24A] px-2 py-1 text-xs font-semibold text-[#0B1F3A] hover:bg-[#D8B75F]" x-text="task.actionLabel"></button>
            </div>
        </article>
    </template>

    <div class="flex flex-col gap-2 border-t border-slate-100 bg-slate-50 px-4 py-3 text-xs text-slate-500 sm:flex-row sm:items-center sm:justify-between">
        <span>Showing <span class="font-semibold text-[#0B1F3A]" x-text="filteredTasks.length"></span> tasks</span>
    </div>
</div>
