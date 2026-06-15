<section @class([
    'space-y-6' => ! ($embedded ?? false),
    'flex min-h-full flex-1 flex-col gap-4 p-4' => ($embedded ?? false),
])>
        <div @class([
            'flex flex-col gap-4 rounded-lg border border-slate-200 bg-white shadow-sm lg:flex-row lg:items-center lg:justify-between',
            'shrink-0 p-4' => ($embedded ?? false),
            'p-6' => ! ($embedded ?? false),
        ])>
            <div @class(['min-w-0' => ($embedded ?? false)])>
                @unless ($embedded ?? false)
                    <p class="text-sm font-semibold uppercase tracking-wide text-[#C8A24A]">Admin Management</p>
                @endunless
                @if ($embedded ?? false)
                    <h1 class="sr-only">{{ $config['label'] }}</h1>
                @else
                    <h1 class="mt-2 text-2xl font-semibold text-[#0B1F3A]">{{ $config['label'] }}</h1>
                    <p class="mt-2 text-sm leading-6 text-slate-600">{{ $config['description'] }}</p>
                @endif
            </div>
            @if ($canManage)
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.management.create', $resource) }}" class="inline-flex items-center justify-center rounded-md bg-[#C8A24A] px-4 py-2 text-sm font-semibold text-[#0B1F3A] transition hover:bg-[#D8B75F]">
                        {{ $canUpdateSeeder ? 'Add Item' : 'Add Record' }}
                    </a>
                    @if ($canUpdateSeeder)
                        <form method="POST" action="{{ route('admin.management.update-seeder', [$resource, 'trashed' => $filters['trashed']]) }}">
                            @csrf
                            <button class="inline-flex items-center justify-center rounded-md border border-[#C8A24A] bg-white px-4 py-2 text-sm font-semibold text-[#0B1F3A] transition hover:bg-[#C8A24A]/10">
                                Update Seeder
                            </button>
                        </form>
                    @endif
                </div>
            @endif
        </div>

        @if (session('status'))
            <div @class([
                'rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800',
                'shrink-0' => ($embedded ?? false),
            ])>
                {{ str(session('status'))->replace('-', ' ')->title() }}
            </div>
        @endif

        <form method="GET" @class([
            'grid gap-3 rounded-lg border border-slate-200 bg-white p-4 shadow-sm md:grid-cols-[1fr_auto_auto]',
            'shrink-0' => ($embedded ?? false),
        ])>
            @if ($embedded ?? false)
                <input type="hidden" name="embedded" value="1">
            @endif
            <input
                name="search"
                value="{{ $filters['search'] }}"
                placeholder="Search {{ strtolower($config['label']) }}"
                class="rounded-md border-slate-300 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]"
            >
            <select name="trashed" class="rounded-md border-slate-300 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
                <option value="" @selected($filters['trashed'] === '')>Active records</option>
                <option value="with" @selected($filters['trashed'] === 'with')>Active and archived</option>
                <option value="only" @selected($filters['trashed'] === 'only')>Archived only</option>
            </select>
            <button class="rounded-md bg-[#0B1F3A] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#13345f]">Filter</button>
        </form>

        <div @class([
            'overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm',
            'flex min-h-0 flex-1 flex-col' => ($embedded ?? false),
        ])>
            @php($booleanColumns = collect($config['fields'])->where('type', 'boolean')->pluck('name')->all())
            @php($hasActiveColumn = collect($config['fields'])->contains(fn ($field) => $field['name'] === 'is_active'))
            <div @class([
                'overflow-x-auto',
                'min-h-0 flex-1 overflow-y-auto' => ($embedded ?? false),
            ])>
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            @foreach ($config['columns'] as $column)
                                <th class="px-4 py-3">{{ str($column)->replace('_', ' ')->title() }}</th>
                            @endforeach
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($records as $record)
                            <tr x-data="{ viewOpen: false, editOpen: false }">
                                @foreach ($config['columns'] as $column)
                                    @php($value = data_get($record, $column))
                                    <td class="max-w-xs px-4 py-3 text-slate-700">
                                        @if (in_array($column, $booleanColumns, true))
                                            <span class="rounded-full {{ (bool) $value ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }} px-2 py-1 text-xs font-semibold">
                                                {{ (bool) $value ? 'Yes' : 'No' }}
                                            </span>
                                        @elseif ($column === 'country')
                                            <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-700">
                                                {{ $value ?: 'Global' }}
                                            </span>
                                        @elseif ($column === 'title' && filled(data_get($record, 'description')))
                                            <span class="flex min-w-0 items-center gap-1.5">
                                                <span class="line-clamp-2">{{ $value ?: 'N/A' }}</span>
                                                <span class="group relative inline-flex shrink-0">
                                                    <span class="inline-flex h-5 w-5 items-center justify-center rounded-full border border-[#C8A24A]/40 bg-[#C8A24A]/10 text-xs font-bold text-[#8A6A1F]">
                                                        ?
                                                    </span>
                                                    <span class="pointer-events-none absolute left-1/2 top-7 z-20 w-72 -translate-x-1/2 rounded-md bg-[#0B1F3A] px-3 py-2 text-xs font-medium leading-5 text-white opacity-0 shadow-lg transition group-hover:opacity-100">
                                                        {{ data_get($record, 'description') }}
                                                    </span>
                                                </span>
                                            </span>
                                        @else
                                            <span class="line-clamp-2">{{ $value ?: 'N/A' }}</span>
                                        @endif
                                    </td>
                                @endforeach
                                <td class="px-4 py-3">
                                    <span class="rounded-full {{
                                        $record->deleted_at
                                            ? 'bg-amber-50 text-amber-700'
                                            : (($hasActiveColumn && ! (bool) $record->is_active) ? 'bg-slate-100 text-slate-600' : 'bg-emerald-50 text-emerald-700')
                                    }} px-2 py-1 text-xs font-semibold">
                                        {{ $record->deleted_at ? 'Archived' : (($hasActiveColumn && ! (bool) $record->is_active) ? 'Inactive' : 'Active') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-1.5">
                                        <a
                                            href="{{ route('admin.management.show', [$resource, $record->id]) }}"
                                            x-on:click.prevent="viewOpen = true"
                                            title="View"
                                            aria-label="View record"
                                            class="group relative inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-slate-50 text-slate-600 transition hover:border-[#C8A24A] hover:bg-[#C8A24A]/10 hover:text-[#0B1F3A]"
                                        >
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z" />
                                                <circle cx="12" cy="12" r="3" />
                                            </svg>
                                            <span class="sr-only">View</span>
                                            <span class="pointer-events-none absolute -top-9 right-0 z-10 whitespace-nowrap rounded-md bg-[#0B1F3A] px-2 py-1 text-xs font-semibold text-white opacity-0 shadow-sm transition group-hover:opacity-100">View</span>
                                        </a>
                                        @if ($canManage)
                                            <a
                                                href="{{ route('admin.management.edit', [$resource, $record->id]) }}"
                                                x-on:click.prevent="editOpen = true"
                                                title="Edit"
                                                aria-label="Edit record"
                                                class="group relative inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-slate-50 text-slate-600 transition hover:border-[#C8A24A] hover:bg-[#C8A24A]/10 hover:text-[#0B1F3A]"
                                            >
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                    <path d="M12 20h9" />
                                                    <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z" />
                                                </svg>
                                                <span class="sr-only">Edit</span>
                                                <span class="pointer-events-none absolute -top-9 right-0 z-10 whitespace-nowrap rounded-md bg-[#0B1F3A] px-2 py-1 text-xs font-semibold text-white opacity-0 shadow-sm transition group-hover:opacity-100">Edit</span>
                                            </a>
                                            @if ($hasActiveColumn && ! $record->deleted_at)
                                                <form method="POST" action="{{ route('admin.management.status', [$resource, $record->id, 'trashed' => $filters['trashed']]) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    @php($statusLabel = (bool) $record->is_active ? 'Deactivate' : 'Activate')
                                                    <button
                                                        title="{{ $statusLabel }}"
                                                        aria-label="{{ $statusLabel }} record"
                                                        class="group relative inline-flex h-9 w-9 items-center justify-center rounded-full border {{ (bool) $record->is_active ? 'border-amber-200 bg-amber-50 text-amber-700 hover:bg-amber-100' : 'border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }} transition"
                                                    >
                                                        @if ((bool) $record->is_active)
                                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                                <circle cx="12" cy="12" r="10" />
                                                                <path d="m4.9 4.9 14.2 14.2" />
                                                            </svg>
                                                        @else
                                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                                <path d="M20 6 9 17l-5-5" />
                                                            </svg>
                                                        @endif
                                                        <span class="sr-only">{{ $statusLabel }}</span>
                                                        <span class="pointer-events-none absolute -top-9 right-0 z-10 whitespace-nowrap rounded-md bg-[#0B1F3A] px-2 py-1 text-xs font-semibold text-white opacity-0 shadow-sm transition group-hover:opacity-100">{{ $statusLabel }}</span>
                                                    </button>
                                                </form>
                                            @endif
                                            @if ($record->deleted_at)
                                                <form method="POST" action="{{ route('admin.management.restore', [$resource, $record->id]) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button
                                                        title="Restore"
                                                        aria-label="Restore record"
                                                        class="group relative inline-flex h-9 w-9 items-center justify-center rounded-full border border-emerald-200 bg-emerald-50 text-emerald-700 transition hover:bg-emerald-100"
                                                    >
                                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                            <path d="M3 12a9 9 0 1 0 3-6.7" />
                                                            <path d="M3 4v6h6" />
                                                        </svg>
                                                        <span class="sr-only">Restore</span>
                                                        <span class="pointer-events-none absolute -top-9 right-0 z-10 whitespace-nowrap rounded-md bg-[#0B1F3A] px-2 py-1 text-xs font-semibold text-white opacity-0 shadow-sm transition group-hover:opacity-100">Restore</span>
                                                    </button>
                                                </form>
                                            @else
                                                <form method="POST" action="{{ route('admin.management.destroy', [$resource, $record->id]) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button
                                                        title="Archive"
                                                        aria-label="Archive record"
                                                        class="group relative inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-slate-50 text-slate-600 transition hover:bg-slate-100 hover:text-[#0B1F3A]"
                                                    >
                                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                            <rect x="3" y="4" width="18" height="4" rx="1" />
                                                            <path d="M5 8v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8" />
                                                            <path d="M10 12h4" />
                                                        </svg>
                                                        <span class="sr-only">Archive</span>
                                                        <span class="pointer-events-none absolute -top-9 right-0 z-10 whitespace-nowrap rounded-md bg-[#0B1F3A] px-2 py-1 text-xs font-semibold text-white opacity-0 shadow-sm transition group-hover:opacity-100">Archive</span>
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                    </div>

                                    <div
                                        x-show="viewOpen"
                                        x-cloak
                                        x-transition.opacity
                                        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 p-4"
                                        role="dialog"
                                        aria-modal="true"
                                        aria-labelledby="view-record-title-{{ $record->id }}"
                                        x-on:keydown.escape.window="viewOpen = false"
                                    >
                                        <div class="absolute inset-0" x-on:click="viewOpen = false"></div>
                                        <div class="relative max-h-[94vh] w-full max-w-3xl overflow-y-auto rounded-lg bg-white shadow-2xl">
                                            <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-6 py-4">
                                                <div>
                                                    <p class="text-xs font-semibold uppercase tracking-wide text-[#C8A24A]">View Record</p>
                                                    <h2 id="view-record-title-{{ $record->id }}" class="mt-1 text-xl font-semibold text-[#0B1F3A]">{{ data_get($record, 'title') ?? data_get($record, 'name') ?? data_get($record, 'code') ?? 'Record Details' }}</h2>
                                                </div>
                                                <button type="button" x-on:click="viewOpen = false" class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 text-slate-600 transition hover:bg-slate-200 hover:text-[#0B1F3A]" aria-label="Close view modal">
                                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                        <path d="M18 6 6 18" />
                                                        <path d="m6 6 12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <div class="px-6 py-5">
                                                <dl class="grid gap-4 md:grid-cols-2">
                                                    @foreach ($config['fields'] as $field)
                                                        @php($viewValue = data_get($record, $field['name']))
                                                        <div class="{{ $field['type'] === 'textarea' ? 'md:col-span-2' : '' }}">
                                                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $field['label'] }}</dt>
                                                            <dd class="mt-2 rounded-md bg-slate-50 px-3 py-2 text-sm leading-6 text-slate-800">
                                                                @if ($field['type'] === 'boolean')
                                                                    <span class="rounded-full {{ (bool) $viewValue ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-200 text-slate-700' }} px-2 py-1 text-xs font-semibold">
                                                                        {{ (bool) $viewValue ? 'Yes' : 'No' }}
                                                                    </span>
                                                                @elseif ($field['name'] === 'country')
                                                                    {{ $viewValue ?: 'Global - all countries' }}
                                                                @else
                                                                    {{ $viewValue ?: 'N/A' }}
                                                                @endif
                                                            </dd>
                                                        </div>
                                                    @endforeach
                                                </dl>
                                            </div>
                                            <div class="flex justify-end gap-3 border-t border-slate-200 px-6 py-4">
                                                <button type="button" x-on:click="viewOpen = false" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Close</button>
                                                @if ($canManage)
                                                    <button type="button" x-on:click="viewOpen = false; editOpen = true" class="rounded-md bg-[#C8A24A] px-4 py-2 text-sm font-semibold text-[#0B1F3A] hover:bg-[#D8B75F]">Edit Item</button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    @if ($canManage)
                                        <div
                                            x-show="editOpen"
                                            x-cloak
                                            x-transition.opacity
                                            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 p-4"
                                            role="dialog"
                                            aria-modal="true"
                                            aria-labelledby="edit-record-title-{{ $record->id }}"
                                            x-on:keydown.escape.window="editOpen = false"
                                        >
                                            <div class="absolute inset-0" x-on:click="editOpen = false"></div>
                                            <div class="relative max-h-[94vh] w-full max-w-3xl overflow-y-auto rounded-lg bg-white shadow-2xl">
                                                <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-6 py-4">
                                                    <div>
                                                        <p class="text-xs font-semibold uppercase tracking-wide text-[#C8A24A]">Edit Record</p>
                                                        <h2 id="edit-record-title-{{ $record->id }}" class="mt-1 text-xl font-semibold text-[#0B1F3A]">{{ data_get($record, 'title') ?? data_get($record, 'name') ?? data_get($record, 'code') ?? 'Record Details' }}</h2>
                                                    </div>
                                                    <button type="button" x-on:click="editOpen = false" class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 text-slate-600 transition hover:bg-slate-200 hover:text-[#0B1F3A]" aria-label="Close edit modal">
                                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                            <path d="M18 6 6 18" />
                                                            <path d="m6 6 12 12" />
                                                        </svg>
                                                    </button>
                                                </div>
                                                <form method="POST" action="{{ route('admin.management.update', [$resource, $record->id]) }}" class="space-y-5 px-6 py-5">
                                                    @csrf
                                                    @method('PATCH')
                                                    @php($fieldIdPrefix = 'modal_'.$resource.'_'.$record->id)
                                                    @include('admin.management.partials.form')
                                                    @php($fieldIdPrefix = null)
                                                    <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:justify-end">
                                                        <button type="button" x-on:click="editOpen = false" class="inline-flex justify-center rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</button>
                                                        <button class="inline-flex justify-center rounded-md bg-[#C8A24A] px-4 py-2 text-sm font-semibold text-[#0B1F3A] hover:bg-[#D8B75F]">Save Changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($config['columns']) + 2 }}" class="px-4 py-10 text-center text-sm text-slate-500">
                                    No records found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-200 px-4 py-3">
                {{ $records->links() }}
            </div>
        </div>
</section>
