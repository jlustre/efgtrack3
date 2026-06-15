<x-guest-layout>
    <div class="min-h-screen bg-[#0B1F3A] px-4 py-8 text-slate-100 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-4xl">
            <header class="mb-8 text-center">
                <a href="/" class="inline-block text-2xl font-extrabold tracking-tight text-white">
                    EFG<span class="text-[#C8A24A]">Track</span>
                </a>
                <p class="mt-2 text-sm font-semibold uppercase tracking-[0.14em] text-[#C8A24A]">Secure Client FNA Portal</p>
                @if (! empty($title))
                    <h1 class="mt-4 text-2xl font-semibold text-white">{{ $title }}</h1>
                @endif
                @if (! empty($invite?->sender?->name))
                    <p class="mt-2 text-sm text-slate-300">Prepared by {{ $invite->sender->name }}</p>
                @endif
                @if (! empty($invite?->personal_message))
                    <div class="mx-auto mt-4 max-w-xl rounded-xl border border-[#C8A24A]/30 bg-white/5 px-4 py-3 text-sm text-slate-200">
                        {{ $invite->personal_message }}
                    </div>
                @endif
            </header>

            @if (session('fna_client_status'))
                <div class="mb-6 rounded-xl border border-emerald-400/40 bg-emerald-500/10 px-4 py-3 text-sm font-medium text-emerald-100">
                    {{ session('fna_client_status') }}
                </div>
            @endif

            <div class="rounded-2xl border border-[#C8A24A]/25 bg-white shadow-xl">
                <div class="border-b border-slate-200 bg-gradient-to-r from-[#0B1F3A] to-[#12345B] px-6 py-4 text-white">
                    <p class="text-sm text-slate-200">Your information is saved automatically as you go. You can return anytime using the same secure link.</p>
                </div>
                <div class="p-6 text-slate-900">
                    {{ $slot }}
                </div>
            </div>

            <p class="mt-6 text-center text-xs text-slate-400">
                Already started? <a href="{{ route('fna.client.return') }}" class="font-semibold text-[#C8A24A] hover:underline">Return to your FNA</a>
            </p>
        </div>
    </div>
</x-guest-layout>
