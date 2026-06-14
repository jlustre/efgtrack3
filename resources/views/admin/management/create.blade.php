<x-app-layout>
    <section class="space-y-6">
        <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-semibold uppercase tracking-wide text-[#C8A24A]">Admin Management</p>
            <div class="mt-2 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <h1 class="text-2xl font-semibold text-[#0B1F3A]">Add {{ str($config['label'])->singular() }}</h1>
                <a href="{{ route('admin.management.resource.index', $resource) }}" class="inline-flex shrink-0 items-center justify-center self-start rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 sm:self-center">
                    Back to List
                </a>
            </div>
            <p class="mt-2 text-sm leading-6 text-slate-600">{{ $config['description'] }}</p>
        </div>

        @if ($resource === 'email-templates')
            @include('admin.management.partials.email-template-tokens')
        @endif

        @if ($resource === 'resources')
            @include('admin.management.partials.resource-document-panel')
        @endif

        <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <form method="POST" action="{{ route('admin.management.store', $resource) }}" class="space-y-5">
                @csrf
                @include('admin.management.partials.form')
                @if ($resource === 'resources')
                    <label class="flex items-start gap-3 rounded-md border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">
                        <input
                            type="checkbox"
                            name="generate_pdf"
                            value="1"
                            class="mt-1 rounded border-slate-300 text-[#C8A24A] focus:ring-[#C8A24A]"
                            @checked(old('generate_pdf', true))
                        >
                        <span>
                            <span class="font-semibold text-[#0B1F3A]">Generate PDF on save</span>
                            <span class="mt-1 block text-slate-600">Create the record and convert the document content into a PDF file.</span>
                        </span>
                    </label>
                @endif
                <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:justify-end">
                    <a href="{{ route('admin.management.resource.index', $resource) }}" class="inline-flex justify-center rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</a>
                    <button class="inline-flex justify-center rounded-md bg-[#C8A24A] px-4 py-2 text-sm font-semibold text-[#0B1F3A] hover:bg-[#D8B75F]">Create Record</button>
                </div>
            </form>
        </div>
    </section>

    @if ($resource === 'email-templates' || $resource === 'resources')
        @include('partials.rich-text-editor-scripts')
    @endif
</x-app-layout>
