<x-app-layout>
    <section class="space-y-6">
        <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-semibold uppercase tracking-wide text-[#C8A24A]">Admin Management</p>
            <div class="mt-2 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <h1 class="text-2xl font-semibold text-[#0B1F3A]">View {{ str($config['label'])->singular() }}</h1>
                <div class="flex shrink-0 flex-wrap gap-2 self-start sm:self-center">
                    <a href="{{ route('admin.management.resource.index', $resource) }}" class="inline-flex items-center justify-center rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        Back to List
                    </a>
                    @if ($canManage)
                        <a href="{{ route('admin.management.edit', [$resource, $record->id]) }}" class="inline-flex items-center justify-center rounded-md bg-[#C8A24A] px-4 py-2 text-sm font-semibold text-[#0B1F3A] hover:bg-[#D8B75F]">
                            Edit Item
                        </a>
                    @endif
                </div>
            </div>
            <p class="mt-2 text-sm leading-6 text-slate-600">{{ $config['description'] }}</p>
        </div>

        <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <dl class="grid gap-5 md:grid-cols-2">
                @foreach ($config['fields'] as $field)
                    @php($value = data_get($record, $field['name']))
                    <div class="{{ in_array($field['type'], ['textarea', 'rich_text'], true) ? 'md:col-span-2' : '' }}">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $field['label'] }}</dt>
                        <dd class="mt-2 rounded-md bg-slate-50 px-3 py-2 text-sm leading-6 text-slate-800">
                            @if ($field['type'] === 'boolean')
                                <span class="rounded-full {{ (bool) $value ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-200 text-slate-700' }} px-2 py-1 text-xs font-semibold">
                                    {{ (bool) $value ? 'Yes' : 'No' }}
                                </span>
                            @elseif ($field['type'] === 'rich_text')
                                <div class="max-w-none text-sm leading-6 text-slate-800 [&_a]:text-[#0B1F3A] [&_a]:underline [&_p+p]:mt-3 [&_ul]:list-disc [&_ul]:pl-5">
                                    {!! $value ?: '<span class="text-slate-500">N/A</span>' !!}
                                </div>
                            @elseif ($field['name'] === 'country')
                                {{ $value ?: 'Global - all countries' }}
                            @else
                                {{ $value ?: 'N/A' }}
                            @endif
                        </dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </section>
</x-app-layout>
