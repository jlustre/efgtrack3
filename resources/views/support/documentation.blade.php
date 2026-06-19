<x-app-layout>
    <div class="space-y-6">
        <div>
            <a href="{{ route('support.index') }}" class="text-sm font-semibold text-[#8A6A1F] underline decoration-[#C8A24A] underline-offset-2 hover:text-[#0B1F3A]">← Back to Help & Support</a>
        </div>

        <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 bg-[#0B1F3A] px-6 py-5 text-white">
                <p class="text-xs font-semibold uppercase tracking-wide text-[#C8A24A]">User Guide</p>
                <h1 class="mt-1 text-2xl font-semibold">{{ $title }}</h1>
                @if ($summary)
                    <p class="mt-2 max-w-3xl text-sm text-slate-300">{{ $summary }}</p>
                @endif
            </div>
            <div class="documentation-prose prose prose-slate max-w-none px-6 py-8 prose-headings:text-[#0B1F3A] prose-a:font-semibold prose-a:text-[#8A6A1F] prose-a:underline prose-a:decoration-[#C8A24A] prose-a:underline-offset-2 hover:prose-a:text-[#0B1F3A] hover:prose-a:decoration-[#8A6A1F]">
                {!! $content !!}
            </div>
        </section>
    </div>
</x-app-layout>
