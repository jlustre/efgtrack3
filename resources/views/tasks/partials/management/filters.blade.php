<div x-show="showFilters" x-transition class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        <div class="sm:col-span-2 lg:col-span-2">
            <label class="mb-1 block text-[11px] font-medium uppercase tracking-wide text-slate-500">Search tasks</label>
            <input type="search" x-model="searchQuery" placeholder="Search tasks..." class="w-full rounded-md border-slate-300 text-sm text-[#0B1F3A] shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]" />
        </div>
        <div>
            <label class="mb-1 block text-[11px] font-medium uppercase tracking-wide text-slate-500">Status</label>
            <select x-model="filterStatus" class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
                <option value="">All Status</option>
                <option>To Do</option><option>In Progress</option><option>Waiting</option><option>Completed</option><option>Cancelled</option><option>Overdue</option>
            </select>
        </div>
        <div>
            <label class="mb-1 block text-[11px] font-medium uppercase tracking-wide text-slate-500">Priority</label>
            <select x-model="filterPriority" class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
                <option value="">All Priority</option>
                <option>Low</option><option>Medium</option><option>High</option><option>Urgent</option>
            </select>
        </div>
        <div>
            <label class="mb-1 block text-[11px] font-medium uppercase tracking-wide text-slate-500">Assigned To</label>
            <select x-model="filterAssignee" class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
                <option value="">All Members</option>
                <option :value="currentUserName" x-text="currentUserName"></option>
            </select>
        </div>
        <div>
            <label class="mb-1 block text-[11px] font-medium uppercase tracking-wide text-slate-500">Category</label>
            <select x-model="filterCategory" class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
                <option value="">All Categories</option>
                <template x-for="cat in categories" :key="cat"><option :value="cat" x-text="cat"></option></template>
            </select>
        </div>
        <div>
            <label class="mb-1 block text-[11px] font-medium uppercase tracking-wide text-slate-500">Due Date</label>
            <input type="date" x-model="filterDueDate" class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]" />
        </div>
        <div>
            <label class="mb-1 block text-[11px] font-medium uppercase tracking-wide text-slate-500">Related Module</label>
            <select x-model="filterModule" class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
                <option value="">All Modules</option>
                <option>Prospects</option><option>Licensing</option><option>Training</option><option>Team</option><option>Rank Advancement</option>
            </select>
        </div>
    </div>
    <div class="mt-4 flex justify-end gap-2">
        <button type="button" @click="clearFilters()" class="rounded-md border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-600 transition hover:border-[#C8A24A] hover:text-[#0B1F3A]">Clear Filters</button>
        <button type="button" class="rounded-md bg-[#C8A24A] px-3 py-1.5 text-xs font-semibold text-[#0B1F3A] transition hover:bg-[#D8B75F]">Apply</button>
    </div>
</div>
