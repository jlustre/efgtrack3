@php
    $trackers = $home['progress'] ?? [];

    $themeFor = fn (string $key): array => match ($key) {
        'goals' => ['track' => 'bg-sky-100', 'fill' => 'bg-sky-500', 'accent' => 'text-sky-700'],
        'licensing' => ['track' => 'bg-red-100', 'fill' => 'bg-red-500', 'accent' => 'text-red-700'],
        'fap' => ['track' => 'bg-emerald-100', 'fill' => 'bg-emerald-500', 'accent' => 'text-emerald-700'],
        'training' => ['track' => 'bg-violet-100', 'fill' => 'bg-violet-500', 'accent' => 'text-violet-700'],
        default => ['track' => 'bg-slate-100', 'fill' => 'bg-[#C8A24A]', 'accent' => 'text-[#8A6A1F]'],
    };
@endphp

<section>
    <h2 class="mb-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Progress Trackers</h2>
    <div class="grid auto-rows-fr gap-3 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($trackers as $tracker)
            @php
                $theme = $themeFor($tracker['key'] ?? '');
                $percent = (int) ($tracker['percent'] ?? 0);
                $restricted = (bool) ($tracker['restricted'] ?? false);
                $url = filled($tracker['route'] ?? null) && ! $restricted ? route($tracker['route']) : null;
            @endphp

            @if ($url)
                <a href="{{ $url }}" class="block h-full rounded-lg transition hover:scale-[1.01] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C8A24A]">
                    @include('dashboard.partials.progress-tracker-card', compact('tracker', 'theme', 'percent', 'restricted'))
                </a>
            @else
                @include('dashboard.partials.progress-tracker-card', compact('tracker', 'theme', 'percent', 'restricted'))
            @endif
        @endforeach
    </div>
</section>
