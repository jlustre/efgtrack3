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
                <div class="flex shrink-0 flex-nowrap items-center gap-2">
                    <a href="{{ route('admin.management.create', $resource) }}" class="inline-flex items-center justify-center whitespace-nowrap rounded-md bg-[#C8A24A] px-4 py-2 text-sm font-semibold text-[#0B1F3A] transition hover:bg-[#D8B75F]">
                        {{ $canUpdateSeeder ? 'Add Item' : 'Add Record' }}
                    </a>
                    @if ($canUpdateSeeder)
                        <form method="POST" class="inline-flex" action="{{ route('admin.management.update-seeder', array_filter([
                            $resource,
                            'search' => $filters['search'] ?? null,
                            'trashed' => $filters['trashed'] ?? null,
                            'checklist_type' => $filters['checklist_type'] ?? null,
                        ])) }}">
                            @csrf
                            <button class="inline-flex items-center justify-center whitespace-nowrap rounded-md border border-[#C8A24A] bg-white px-4 py-2 text-sm font-semibold text-[#0B1F3A] transition hover:bg-[#C8A24A]/10">
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

        @if (session('error'))
            <div @class([
                'rounded-md border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-medium text-amber-900',
                'shrink-0' => ($embedded ?? false),
            ])>
                {{ session('error') }}
            </div>
        @endif

        @if ($resource === 'email-templates')
            @include('admin.management.partials.email-template-tokens')
        @endif

        <form method="GET" @class([
            'grid gap-3 rounded-lg border border-slate-200 bg-white p-4 shadow-sm',
            'shrink-0' => ($embedded ?? false),
            'md:grid-cols-[1fr_auto_auto_auto]' => in_array($resource, ['resources', 'checklists'], true),
            'md:grid-cols-[1fr_auto_auto]' => ! in_array($resource, ['resources', 'checklists'], true),
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
            @if ($resource === 'resources')
                <select name="category" class="rounded-md border-slate-300 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
                    <option value="" @selected(($filters['category'] ?? '') === '')>All categories</option>
                    @foreach (\App\Support\ResourceDocumentCategories::optionsForSelect() as $categoryKey => $categoryLabel)
                        <option value="{{ $categoryKey }}" @selected(($filters['category'] ?? '') === $categoryKey)>{{ $categoryLabel }}</option>
                    @endforeach
                </select>
            @endif
            @if ($resource === 'checklists')
                <select name="checklist_type" class="rounded-md border-slate-300 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
                    <option value="" @selected(($filters['checklist_type'] ?? '') === '')>All checklist types</option>
                    @foreach ($checklistTypes as $type)
                        <option value="{{ $type->id }}" @selected((string) ($filters['checklist_type'] ?? '') === (string) $type->id)>{{ $type->name }}</option>
                    @endforeach
                </select>
            @endif
            <select name="trashed" class="rounded-md border-slate-300 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
                <option value="" @selected($filters['trashed'] === '')>Active records</option>
                <option value="with" @selected($filters['trashed'] === 'with')>Active and archived</option>
                <option value="only" @selected($filters['trashed'] === 'only')>Archived only</option>
            </select>
            <button class="rounded-md bg-[#0B1F3A] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#13345f]">Filter</button>
        </form>

        @if ($resource === 'resources')
            <div @class([
                'overflow-hidden rounded-lg border border-[#C8A24A]/30 bg-white shadow-sm',
                'shrink-0' => ($embedded ?? false),
            ])>
                <div class="border-b border-[#C8A24A]/20 bg-[#C8A24A]/5 px-4 py-3">
                    <h2 class="text-sm font-semibold text-[#0B1F3A]">My Favorites</h2>
                    <p class="mt-1 text-xs text-slate-600">Documents you have starred for quick access.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-4 py-3">Title</th>
                                <th class="px-4 py-3">Category</th>
                                <th class="px-4 py-3">Published</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($favoriteRecords as $favorite)
                                <tr>
                                    <td class="max-w-xs px-4 py-3 text-slate-700">
                                        <span class="line-clamp-2">{{ $favorite->title ?: 'N/A' }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-slate-700">{{ $favorite->category ?: 'N/A' }}</td>
                                    <td class="px-4 py-3">
                                        <span class="rounded-full {{ $favorite->is_published ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }} px-2 py-1 text-xs font-semibold">
                                            {{ $favorite->is_published ? 'Yes' : 'No' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="rounded-full {{ $favorite->trashed() ? 'bg-amber-50 text-amber-700' : 'bg-emerald-50 text-emerald-700' }} px-2 py-1 text-xs font-semibold">
                                            {{ $favorite->trashed() ? 'Archived' : 'Active' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex justify-end gap-1.5">
                                            @include('admin.management.partials.resource-favorite-button', [
                                                'recordId' => $favorite->id,
                                                'isFavorited' => true,
                                                'filters' => $filters,
                                                'embedded' => $embedded ?? false,
                                            ])
                                            @if ($canManage)
                                                <a
                                                    href="{{ route('admin.management.edit', ['resources', $favorite->id]) }}"
                                                    title="Edit"
                                                    aria-label="Edit record"
                                                    class="group relative inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-slate-50 text-slate-600 transition hover:border-[#C8A24A] hover:bg-[#C8A24A]/10 hover:text-[#0B1F3A]"
                                                >
                                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                        <path d="M12 20h9" />
                                                        <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z" />
                                                    </svg>
                                                    <span class="sr-only">Edit</span>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">
                                        No favorites yet. Star a document in the list below to add it here.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <div @class([
            'overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm',
            'flex min-h-0 flex-1 flex-col' => ($embedded ?? false),
        ])>
            @php($booleanColumns = collect($config['fields'])->where('type', 'boolean')->pluck('name')->all())
            @php($hasActiveColumn = collect($config['fields'])->contains(fn ($field) => $field['name'] === 'is_active'))
            @php($useInlineModals = $config['use_inline_modals'] ?? true)
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
                            @php($isUploadedPdf = ($resource === 'resources') ? \App\Models\PortalResource::isUploadedPdfAttributes($record->file_path ?? null, $record->file_format ?? null, $record->content ?? null) : false)
                            <tr @if ($useInlineModals) x-data="{ viewOpen: false, editOpen: false }" @endif>
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
                                        @elseif ($column === 'checklist_type_id' && $resource === 'checklists')
                                            <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-700">
                                                {{ $checklistTypes->firstWhere('id', $value)?->name ?? 'N/A' }}
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
                                        @if ($resource === 'resources' && ($record->type ?? 'document') === 'document')
                                            @include('admin.management.partials.resource-favorite-button', [
                                                'recordId' => $record->id,
                                                'isFavorited' => in_array($record->id, $favoriteResourceIds ?? [], true),
                                                'filters' => $filters,
                                                'embedded' => $embedded ?? false,
                                            ])
                                        @elseif (! $isUploadedPdf)
                                            <a
                                                href="{{ route('admin.management.show', [$resource, $record->id]) }}"
                                                @if ($useInlineModals) x-on:click.prevent="viewOpen = true" @endif
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
                                        @endif
                                        @if ($resource === 'resources' && filled($record->file_path) && ! str_starts_with($record->file_path, 'http') && strtoupper($record->file_format ?? 'PDF') === 'PDF')
                                            <a
                                                href="{{ route('admin.management.resources.view-pdf', $record->id, absolute: false) }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                title="View PDF"
                                                aria-label="View PDF"
                                                class="group relative inline-flex h-9 w-9 items-center justify-center rounded-full border border-red-200 bg-red-50 text-red-700 transition hover:border-red-300 hover:bg-red-100 hover:text-red-800"
                                            >
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z" />
                                                    <path d="M14 2v6h6" />
                                                    <path d="M10 13h4" />
                                                    <path d="M10 17h4" />
                                                    <path d="M10 9H8" />
                                                </svg>
                                                <span class="sr-only">View PDF</span>
                                                <span class="pointer-events-none absolute -top-9 right-0 z-10 whitespace-nowrap rounded-md bg-[#0B1F3A] px-2 py-1 text-xs font-semibold text-white opacity-0 shadow-sm transition group-hover:opacity-100">View PDF</span>
                                            </a>
                                        @elseif ($resource === 'resources' && filled($record->url) && str_ends_with(strtolower(parse_url(\App\Support\ResourceUrl::resolve($record->url) ?? '', PHP_URL_PATH) ?? ''), '.pdf'))
                                            <a
                                                href="{{ \App\Support\ResourceUrl::resolve($record->url) }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                title="View PDF"
                                                aria-label="View PDF"
                                                class="group relative inline-flex h-9 w-9 items-center justify-center rounded-full border border-red-200 bg-red-50 text-red-700 transition hover:border-red-300 hover:bg-red-100 hover:text-red-800"
                                            >
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z" />
                                                    <path d="M14 2v6h6" />
                                                    <path d="M10 13h4" />
                                                    <path d="M10 17h4" />
                                                    <path d="M10 9H8" />
                                                </svg>
                                                <span class="sr-only">View PDF</span>
                                                <span class="pointer-events-none absolute -top-9 right-0 z-10 whitespace-nowrap rounded-md bg-[#0B1F3A] px-2 py-1 text-xs font-semibold text-white opacity-0 shadow-sm transition group-hover:opacity-100">View PDF</span>
                                            </a>
                                        @endif
                                        @if ($canManage)
                                            @php($canEditRecord = $resource !== 'resources' || auth()->user()->canUpdateDocument($record))
                                            @if ($canEditRecord)
                                                <a
                                                    href="{{ route('admin.management.edit', [$resource, $record->id]) }}"
                                                    @if ($useInlineModals) x-on:click.prevent="editOpen = true" @endif
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
                                            @elseif ($resource === 'resources')
                                                <button
                                                    type="button"
                                                    title="Read only"
                                                    aria-label="Cannot edit record you do not own"
                                                    class="group relative inline-flex h-9 w-9 items-center justify-center rounded-full border border-amber-200 bg-amber-50 text-amber-700 transition hover:bg-amber-100"
                                                    x-on:click="alert('You can only update documents that you created. Contact an administrator if this record needs changes.')"
                                                >
                                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                        <path d="M12 20h9" />
                                                        <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z" />
                                                        <path d="m2 2 20 20" />
                                                    </svg>
                                                    <span class="sr-only">Cannot edit</span>
                                                    <span class="pointer-events-none absolute -top-9 right-0 z-10 whitespace-nowrap rounded-md bg-[#0B1F3A] px-2 py-1 text-xs font-semibold text-white opacity-0 shadow-sm transition group-hover:opacity-100">Owner only</span>
                                                </button>
                                            @endif
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
                                                @if ($canDeleteRecords)
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
                                                @endif
                                            @else
                                                @if ($canDeleteRecords)
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
                                        @endif
                                    </div>

                                    @if ($useInlineModals)
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
                                                @if ($canEditRecord ?? true)
                                                    <button type="button" x-on:click="viewOpen = false; editOpen = true" class="rounded-md bg-[#C8A24A] px-4 py-2 text-sm font-semibold text-[#0B1F3A] hover:bg-[#D8B75F]">Edit Item</button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    @if ($useInlineModals && $canManage && ($resource !== 'resources' || auth()->user()->canUpdateDocument($record)))
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
