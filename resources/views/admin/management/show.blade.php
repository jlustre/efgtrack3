<x-app-layout>
    <section class="space-y-6">
        <div class="flex flex-col gap-4 rounded-lg border border-slate-200 bg-white p-6 shadow-sm lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-[#C8A24A]">Admin Management</p>
                <h1 class="mt-2 text-2xl font-semibold text-[#0B1F3A]">View {{ str($config['label'])->singular() }}</h1>
                <p class="mt-2 text-sm text-slate-600">{{ $config['description'] }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
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

        <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <dl class="grid gap-5 md:grid-cols-2">
                @foreach ($config['fields'] as $field)
                    @php($value = data_get($record, $field['name']))
                    <div class="{{ $field['type'] === 'textarea' ? 'md:col-span-2' : '' }}">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $field['label'] }}</dt>
                        <dd class="mt-2 rounded-md bg-slate-50 px-3 py-2 text-sm leading-6 text-slate-800">
                            @if ($field['type'] === 'boolean')
                                <span class="rounded-full {{ (bool) $value ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-200 text-slate-700' }} px-2 py-1 text-xs font-semibold">
                                    {{ (bool) $value ? 'Yes' : 'No' }}
                                </span>
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
