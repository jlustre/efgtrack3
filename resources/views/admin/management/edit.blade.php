<x-app-layout>
    <section class="space-y-6">
        <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-semibold uppercase tracking-wide text-[#C8A24A]">Admin Management</p>
            <div class="mt-2 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <h1 class="text-2xl font-semibold text-[#0B1F3A]">Edit {{ str($config['label'])->singular() }}</h1>
                <a href="{{ route('admin.management.resource.index', $resource) }}" class="inline-flex shrink-0 items-center justify-center self-start rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 sm:self-center">
                    Back to List
                </a>
            </div>
            <p class="mt-2 text-sm leading-6 text-slate-600">{{ $config['description'] }}</p>
        </div>

        @if (session('status'))
            <div class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                {{ str(session('status'))->replace('-', ' ')->title() }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-md border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-medium text-amber-900">
                {{ session('error') }}
            </div>
        @endif

        @if ($resource === 'resources' && ! ($canUpdateRecord ?? true))
            <div class="rounded-md border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-950">
                <p class="font-semibold">Read-only access</p>
                <p class="mt-1">You can only update documents that you created. Contact an administrator if this record needs changes.</p>
            </div>
        @endif

        @if ($resource === 'email-templates')
            @include('admin.management.partials.email-template-tokens')
        @endif

        @if ($resource === 'resources')
            @include('admin.management.partials.resource-document-panel', ['canUpdateRecord' => $canUpdateRecord ?? true])
        @endif

        @php
            $contentSource = 'compose';

            if ($resource === 'resources') {
                $contentSource = old('content_source');

                if ($contentSource === null) {
                    $hasUploadedPdf = filled(data_get($record, 'file_path'))
                        && ! str_starts_with((string) data_get($record, 'file_path'), 'http')
                        && blank(strip_tags((string) (data_get($record, 'content') ?? '')));
                    $contentSource = $hasUploadedPdf ? 'upload' : 'compose';
                }
            }
        @endphp

        <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <form
                method="POST"
                action="{{ route('admin.management.update', [$resource, $record->id]) }}"
                enctype="multipart/form-data"
                class="space-y-5"
                @if ($resource === 'resources')
                    x-data="{ contentMode: @js($contentSource) }"
                    @if (! ($canUpdateRecord ?? true))
                        x-on:submit.prevent="alert('You can only update documents that you created. Contact an administrator if this record needs changes.')"
                    @endif
                @endif
            >
                @csrf
                @method('PATCH')
                <fieldset @disabled($resource === 'resources' && ! ($canUpdateRecord ?? true))>
                @include('admin.management.partials.form', ['resource' => $resource, 'record' => $record])
                @if ($resource === 'resources')
                    <label
                        x-show="contentMode === 'compose'"
                        x-cloak
                        class="flex items-start gap-3 rounded-md border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700"
                    >
                        <input
                            type="checkbox"
                            name="generate_pdf"
                            value="1"
                            class="mt-1 rounded border-slate-300 text-[#C8A24A] focus:ring-[#C8A24A]"
                            @checked(old('generate_pdf'))
                        >
                        <span>
                            <span class="font-semibold text-[#0B1F3A]">Generate PDF on save</span>
                            <span class="mt-1 block text-slate-600">Convert the current document content into a stored PDF file after saving.</span>
                        </span>
                    </label>
                @endif
                </fieldset>
                <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:justify-end">
                    <a href="{{ route('admin.management.resource.index', $resource) }}" class="inline-flex justify-center rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</a>
                    @if ($resource !== 'resources' || ($canUpdateRecord ?? true))
                        <button class="inline-flex justify-center rounded-md bg-[#C8A24A] px-4 py-2 text-sm font-semibold text-[#0B1F3A] hover:bg-[#D8B75F]">Save Changes</button>
                    @endif
                </div>
            </form>
        </div>
    </section>

    @if ($resource === 'email-templates' || $resource === 'resources')
        @include('partials.rich-text-editor-scripts')
    @endif
</x-app-layout>
