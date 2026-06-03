<div x-show="selectedTask && mobileDetail" x-cloak class="fixed inset-0 z-40 bg-slate-900/50 xl:hidden" @click="mobileDetail = false">
    <div @click.stop class="absolute inset-x-0 bottom-0 max-h-[85vh] overflow-y-auto rounded-t-2xl border-t border-slate-200 bg-white p-4 shadow-xl">
        <div class="mb-3 flex justify-between">
            <span class="text-xs font-semibold uppercase tracking-wider text-[#C8A24A]">Task Detail</span>
            <button type="button" @click="mobileDetail = false" class="text-sm font-medium text-slate-500 hover:text-[#0B1F3A]">Close</button>
        </div>
        <template x-if="selectedTask">
            <div>
                <h3 class="text-base font-semibold text-[#0B1F3A]" x-text="selectedTask.title"></h3>
                <p class="mt-2 text-xs text-slate-600" x-text="selectedTask.desc"></p>
                <template x-if="selectedTask.actionUrl">
                    <a :href="selectedTask.actionUrl" class="mt-4 inline-flex w-full items-center justify-center rounded-md bg-[#C8A24A] px-4 py-2.5 text-sm font-semibold text-[#0B1F3A] hover:bg-[#D8B75F]" x-text="selectedTask.actionLabel"></a>
                </template>
            </div>
        </template>
    </div>
</div>
