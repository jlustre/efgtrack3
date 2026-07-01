@php
    $welcome = $home['welcome'] ?? [];
    $quote = $home['daily_quote'] ?? null;
@endphp

<section class="overflow-hidden rounded-lg border border-slate-200 bg-gradient-to-br from-[#0B1F3A] via-[#0B1F3A] to-[#132d52] text-white shadow-sm">
    <div class="px-6 py-6 lg:px-8">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
            <div class="flex items-start gap-4">
                <x-user-avatar :user="$user" size="lg" class="border-white/20 bg-white/10" />

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-[#C8A24A]">Welcome Message</p>
                    <h1 class="mt-1 text-2xl font-semibold lg:text-3xl">{{ $welcome['headline'] ?? 'Welcome back.' }}</h1>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-200">{{ $welcome['message'] ?? '' }}</p>

                    <div class="mt-4 flex flex-wrap gap-2">
                        @if (filled($welcome['rank'] ?? null))
                            <span class="rounded-full border border-white/15 bg-white/10 px-3 py-1 text-xs font-semibold">{{ $welcome['rank'] }}</span>
                        @endif
                        @if (filled($welcome['team'] ?? null))
                            <span class="rounded-full border border-[#C8A24A]/40 bg-[#C8A24A]/10 px-3 py-1 text-xs font-semibold text-[#F7E8B8]">{{ $welcome['team'] }}</span>
                        @endif
                    </div>
                </div>
            </div>

            @if (filled($quote))
                <blockquote class="max-w-md rounded-lg border border-white/10 bg-white/5 p-4 lg:mt-0">
                    <p class="text-xs font-semibold uppercase tracking-wide text-[#C8A24A]">Daily Quote</p>
                    <p class="mt-2 text-sm italic leading-6 text-slate-100">&ldquo;{{ $quote['quote'] }}&rdquo;</p>
                    @if (filled($quote['author'] ?? null))
                        <footer class="mt-3 text-xs font-semibold text-slate-300">— {{ $quote['author'] }}</footer>
                    @endif
                </blockquote>
            @endif
        </div>
    </div>
</section>
