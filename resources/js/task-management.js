const defaultCategories = [
    'Prospect Follow-Up',
    'Licensing',
    'Training',
    'CFM Mentorship',
    'Field Apprenticeship',
    'Rank Advancement',
    'Team Meeting',
    'Resource Review',
    'Personal',
    'Admin',
];

const defaultViewTabs = [
    { id: 'list', label: 'List View' },
    { id: 'board', label: 'Board View' },
    { id: 'calendar', label: 'Calendar View' },
    { id: 'my', label: 'My Tasks' },
    { id: 'team', label: 'Team Tasks' },
    { id: 'completed', label: 'Completed' },
];

export default function taskManagement(initial = {}) {
    return {
        activeView: 'list',
        showFilters: false,
        showNewTask: false,
        showFormPanel: true,
        selectedTask: null,
        mobileDetail: false,
        searchQuery: '',
        filterStatus: '',
        filterPriority: '',
        filterAssignee: '',
        filterCategory: '',
        filterDueDate: '',
        filterModule: '',
        newTaskChecklist: [{ text: '' }, { text: '' }],
        currentUserName: initial.currentUserName ?? '',
        viewTabs: initial.viewTabs ?? defaultViewTabs,
        categories: initial.categories ?? defaultCategories,
        tasks: initial.tasks ?? [],
        kanbanCols: [
            { id: 'todo', label: 'To Do', dot: 'bg-zinc-500', tasks: [] },
            { id: 'inprogress', label: 'In Progress', dot: 'bg-blue-400', tasks: [] },
            { id: 'waiting', label: 'Waiting', dot: 'bg-purple-400', tasks: [] },
            { id: 'completed', label: 'Completed', dot: 'bg-emerald-400', tasks: [] },
        ],
        teamMembers: initial.teamMembers ?? [],
        aiSuggestions: initial.aiSuggestions ?? [],
        calendarDays: [],
        stats: initial.stats ?? {
            total: 0,
            dueToday: 0,
            overdue: 0,
            completedWeek: 0,
            highPriority: 0,
            assignedToMe: 0,
        },
        calendarLabel: initial.calendarLabel ?? '',
        reviewSubmitting: false,
        reviewError: null,
        commentSubmitting: false,
        commentError: null,
        commentBody: '',

        get filteredTasks() {
            return this.tasks.filter((task) => {
                if (this.searchQuery && !task.title.toLowerCase().includes(this.searchQuery.toLowerCase())) {
                    return false;
                }

                if (this.filterStatus && task.status !== this.filterStatus) {
                    return false;
                }

                if (this.filterPriority && task.priority !== this.filterPriority) {
                    return false;
                }

                if (this.filterAssignee && task.assignee !== this.filterAssignee) {
                    return false;
                }

                if (this.filterCategory && task.category !== this.filterCategory) {
                    return false;
                }

                if (this.filterModule && task.module !== this.filterModule) {
                    return false;
                }

                if (this.activeView === 'my' && task.assignee !== this.currentUserName) {
                    return false;
                }

                if (this.activeView === 'completed' && task.status !== 'Completed') {
                    return false;
                }

                return true;
            });
        },

        priorityClass(priority) {
            const map = {
                Urgent: 'bg-red-50 text-red-700 border border-red-200',
                High: 'bg-orange-50 text-orange-700 border border-orange-200',
                Medium: 'bg-[#FFF9EA] text-[#8A6A1F] border border-[#C8A24A]/30',
                Low: 'bg-emerald-50 text-emerald-700 border border-emerald-200',
            };

            return map[priority] ?? map.Medium;
        },

        statusClass(status) {
            const map = {
                'To Do': 'bg-slate-100 text-slate-700 border border-slate-200',
                'In Progress': 'bg-blue-50 text-blue-700 border border-blue-200',
                Waiting: 'bg-purple-50 text-purple-700 border border-purple-200',
                Completed: 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                Overdue: 'bg-red-50 text-red-700 border border-red-200',
                Cancelled: 'bg-slate-100 text-slate-500 border border-slate-200',
            };

            return map[status] ?? map['To Do'];
        },

        selectTask(task) {
            this.selectedTask = task;
            this.reviewError = null;
            this.commentError = null;
            this.commentBody = '';

            if (window.innerWidth < 1280) {
                this.mobileDetail = true;
            }
        },

        openTask(task) {
            if (task?.actionUrl) {
                window.location.href = task.actionUrl;
            }
        },

        async submitConfirmationReview(event) {
            const form = event.target;

            if (! (form instanceof HTMLFormElement) || ! this.selectedTask?.reviewUrl) {
                return;
            }

            event.preventDefault();

            const formData = new FormData(form);
            const submitter = event.submitter;

            if (submitter?.name) {
                formData.set(submitter.name, submitter.value);
            }

            this.reviewSubmitting = true;
            this.reviewError = null;

            try {
                const response = await fetch(this.selectedTask.reviewUrl, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                const data = await response.json().catch(() => ({}));

                if (! response.ok) {
                    const validationMessage = data.errors
                        ? Object.values(data.errors).flat()[0]
                        : null;

                    throw new Error(validationMessage || data.message || 'Could not submit confirmation review.');
                }

                const reviewedTaskId = this.selectedTask.id;
                this.tasks = this.tasks.filter((task) => task.id !== reviewedTaskId);
                this.selectedTask = this.tasks[0] ?? null;
                form.reset();
            } catch (error) {
                this.reviewError = error.message || 'Could not submit confirmation review.';
            } finally {
                this.reviewSubmitting = false;
            }
        },

        async submitTaskComment() {
            const task = this.selectedTask;

            if (! task || task.source !== 'database' || ! task.databaseId) {
                return;
            }

            const body = this.commentBody.trim();

            if (! body) {
                this.commentError = 'Enter an activity note before saving.';

                return;
            }

            this.commentSubmitting = true;
            this.commentError = null;

            try {
                const response = await fetch(`/tasks/${task.databaseId}/comments`, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ body }),
                });

                const data = await response.json().catch(() => ({}));

                if (! response.ok) {
                    const validationMessage = data.errors
                        ? Object.values(data.errors).flat()[0]
                        : null;

                    throw new Error(validationMessage || data.message || 'Could not save activity note.');
                }

                task.comments = [data.comment, ...(task.comments ?? [])];
                this.commentBody = '';
            } catch (error) {
                this.commentError = error.message || 'Could not save activity note.';
            } finally {
                this.commentSubmitting = false;
            }
        },

        clearFilters() {
            this.searchQuery = '';
            this.filterStatus = '';
            this.filterPriority = '';
            this.filterAssignee = '';
            this.filterCategory = '';
            this.filterDueDate = '';
            this.filterModule = '';
        },

        buildKanban() {
            const statusMap = {
                'To Do': 'todo',
                'In Progress': 'inprogress',
                Waiting: 'waiting',
                Completed: 'completed',
                Overdue: 'todo',
            };

            this.kanbanCols.forEach((column) => {
                column.tasks = [];
            });

            this.tasks.forEach((task) => {
                const column = this.kanbanCols.find((col) => col.id === (statusMap[task.status] || 'todo'));

                if (column) {
                    column.tasks.push(task);
                }
            });
        },

        buildCalendar() {
            const days = [];
            const firstDay = initial.calendarFirstDay ?? 0;
            const daysInMonth = initial.calendarDaysInMonth ?? 30;
            const today = initial.calendarToday ?? null;
            const events = initial.calendarEvents ?? {};

            for (let index = 0; index < firstDay; index += 1) {
                days.push({ key: `empty-${index}`, n: '', today: false, events: [] });
            }

            for (let day = 1; day <= daysInMonth; day += 1) {
                days.push({
                    key: `day-${day}`,
                    n: day,
                    today: day === today,
                    events: events[day] ?? [],
                });
            }

            this.calendarDays = days;
        },

        init() {
            this.buildKanban();
            this.buildCalendar();

            if (this.tasks.length > 0) {
                this.selectedTask = this.tasks[0];
            }
        },
    };
}
