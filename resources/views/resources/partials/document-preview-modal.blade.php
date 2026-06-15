<div
    x-show="showPreview"
    x-cloak
    class="fixed inset-0 z-[60] overflow-hidden bg-slate-950/70 backdrop-blur-sm"
    @keydown.escape.window="closePreview()"
>
    <div class="flex h-full flex-col">
        <div class="border-b border-slate-800 bg-[#0B1F3A] px-4 py-4 text-white sm:px-6">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-xs font-semibold uppercase tracking-wide text-[#C8A24A]">Document Preview</p>
                    <h3 class="mt-1 text-lg font-semibold" x-text="previewData?.title || 'Loading document…'"></h3>
                    <p class="mt-1 text-sm text-slate-300" x-show="previewData?.description" x-text="previewData?.description"></p>
                </div>
                <div class="flex shrink-0 items-center gap-2">
                    <a
                        x-show="previewData?.download_url"
                        :href="previewData?.download_url"
                        class="hidden rounded-md border border-white/20 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-white/10 sm:inline-flex"
                    >
                        Download
                    </a>
                    <button
                        type="button"
                        @click="closePreview()"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/10 text-xl leading-none text-white transition hover:bg-white/20"
                        aria-label="Close preview"
                    >&times;</button>
                </div>
            </div>

            <div
                x-show="previewData && (previewData.has_rtf || previewData.has_pdf)"
                class="mt-4 inline-flex rounded-lg border border-white/15 bg-white/5 p-1"
            >
                <button
                    type="button"
                    x-show="previewData?.has_rtf"
                    @click="previewView = 'rtf'"
                    class="rounded-md px-4 py-2 text-xs font-semibold transition"
                    :class="previewView === 'rtf' ? 'bg-[#C8A24A] text-[#0B1F3A]' : 'text-slate-200 hover:bg-white/10'"
                >
                    Rich Text
                </button>
                <button
                    type="button"
                    x-show="previewData?.has_pdf"
                    @click="previewView = 'pdf'"
                    class="rounded-md px-4 py-2 text-xs font-semibold transition"
                    :class="previewView === 'pdf' ? 'bg-[#C8A24A] text-[#0B1F3A]' : 'text-slate-200 hover:bg-white/10'"
                >
                    PDF
                </button>
            </div>
        </div>

        <div class="flex-1 overflow-hidden bg-slate-100">
            <div x-show="previewLoading" class="flex h-full items-center justify-center">
                <div class="text-center">
                    <div class="mx-auto h-8 w-8 animate-spin rounded-full border-2 border-[#C8A24A] border-t-transparent"></div>
                    <p class="mt-4 text-sm font-medium text-slate-600">Loading document preview…</p>
                </div>
            </div>

            <div x-show="previewError && ! previewLoading" class="flex h-full items-center justify-center px-6">
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" x-text="previewError"></div>
            </div>

            <template x-if="previewData && ! previewLoading && previewView === 'rtf' && previewData.has_rtf">
                <div class="h-full overflow-y-auto bg-white">
                    <div
                        class="mx-auto max-w-4xl px-6 py-8 text-sm leading-7 text-slate-800 [&_a]:text-[#0B1F3A] [&_a]:underline [&_h2]:mt-6 [&_h2]:text-lg [&_h2]:font-semibold [&_h3]:mt-4 [&_h3]:font-semibold [&_li]:ml-5 [&_ol]:list-decimal [&_p+p]:mt-4 [&_ul]:list-disc"
                        x-html="previewData.rtf_html"
                    ></div>
                </div>
            </template>

            <template x-if="previewData && ! previewLoading && previewView === 'pdf' && previewData.has_pdf">
                <iframe
                    :src="previewData.pdf_url"
                    class="h-full w-full border-0 bg-white"
                    title="PDF document preview"
                ></iframe>
            </template>

            <template x-if="previewData && ! previewLoading && previewData.default_view === 'summary' && ! previewData.has_rtf && ! previewData.has_pdf">
                <div class="flex h-full items-center justify-center px-6">
                    <div class="max-w-lg rounded-xl border border-slate-200 bg-white p-6 text-center shadow-sm">
                        <p class="text-sm leading-6 text-slate-600" x-text="previewData.description || 'This document is available through an external link.'"></p>
                        <a
                            x-show="previewData.external_url"
                            :href="previewData.external_url"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="mt-4 inline-flex rounded-md bg-[#0B1F3A] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#13345f]"
                        >
                            Open document
                        </a>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
