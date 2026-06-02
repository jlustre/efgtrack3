<x-app-layout>
    <section class="space-y-6">
        <div class="flex flex-col gap-4 rounded-lg border border-slate-200 bg-white p-6 shadow-sm lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-[#C8A24A]">Admin Management</p>
                <h1 class="mt-2 text-2xl font-semibold text-[#0B1F3A]">Edit {{ str($config['label'])->singular() }}</h1>
                <p class="mt-2 text-sm text-slate-600">{{ $config['description'] }}</p>
            </div>
            <a href="{{ route('admin.management.resource.index', $resource) }}" class="inline-flex items-center justify-center rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Back to List
            </a>
        </div>

        @if (session('status'))
            <div class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                {{ str(session('status'))->replace('-', ' ')->title() }}
            </div>
        @endif

        <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <form method="POST" action="{{ route('admin.management.update', [$resource, $record->id]) }}" class="space-y-5">
                @csrf
                @method('PATCH')
                @include('admin.management.partials.form')
                <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:justify-end">
                    <a href="{{ route('admin.management.resource.index', $resource) }}" class="inline-flex justify-center rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</a>
                    <button class="inline-flex justify-center rounded-md bg-[#C8A24A] px-4 py-2 text-sm font-semibold text-[#0B1F3A] hover:bg-[#D8B75F]">Save Changes</button>
                </div>
            </form>
        </div>
    </section>
</x-app-layout>
