<x-guest-layout>
    <div class="min-h-screen bg-[radial-gradient(circle_at_10%_20%,#111111,#000000)] px-4 py-8 text-slate-100 sm:px-6 lg:px-8">
        <div class="mx-auto flex min-h-[calc(100vh-4rem)] max-w-6xl items-center justify-center">
            <div class="w-full overflow-hidden rounded-[2rem] border border-[#D4AF37]/25 bg-black/80 shadow-[0_25px_45px_-12px_rgba(0,0,0,0.8),0_0_18px_rgba(212,175,55,0.22)]">
                <div class="grid bg-[#0b0b0c] lg:grid-cols-[0.95fr_1fr]">
                    <aside class="border-b border-[#D4AF37]/25 bg-gradient-to-br from-black to-[#101012] p-6 sm:p-8 lg:border-b-0 lg:border-r">
                        <div>
                            <a href="/" class="inline-block bg-gradient-to-br from-[#F5E7B2] via-[#D4AF37] to-[#B8860B] bg-clip-text text-3xl font-extrabold tracking-normal text-transparent">
                                EFG<span class="font-black">Track</span>.com
                            </a>
                            <div class="mt-2 border-l-2 border-[#D4AF37] pl-3 text-xs font-semibold uppercase tracking-[0.16em] text-[#D4AF37]">
                                Financial Intelligence &middot; Insurance Team Portal
                            </div>
                        </div>

                        <div class="mt-8 overflow-hidden rounded-3xl border border-[#D4AF37]/20 bg-[#070707] shadow-[0_18px_34px_-24px_rgba(212,175,55,0.55)]">
                            <img
                                src="{{ asset('images/authimage.jpg') }}"
                                alt="EFGTrack portal access"
                                class="h-64 w-full object-cover object-top sm:h-80 lg:h-[22rem]"
                                style="-webkit-mask-image: linear-gradient(to bottom, black 0%, black 62%, rgba(0,0,0,0.3) 100%); mask-image: linear-gradient(to bottom, black 0%, black 62%, rgba(0,0,0,0.3) 100%);"
                            >
                        </div>

                        <div class="mt-8">
                            <h1 class="max-w-lg bg-gradient-to-r from-white to-[#E5C56A] bg-clip-text text-3xl font-bold leading-tight text-transparent sm:text-4xl">
                                Welcome Back
                            </h1>
                            <div class="mt-4 h-0.5 w-16 bg-gradient-to-r from-[#D4AF37] to-transparent"></div>
                            <p class="mt-5 max-w-md border-l-2 border-[#D4AF37]/50 pl-4 text-base leading-5 text-slate-300">
                                Continue tracking onboarding, licensing, mentorship, training, team progress, and rank advancement.
                            </p>

                            <div class="mt-4 space-y-1 text-sm text-slate-200">
                                <div class="flex items-center gap-3">
                                    <span class="h-2 w-2 rounded-full bg-[#D4AF37] shadow-[0_0_8px_#D4AF37]"></span>
                                    Private member dashboard
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="h-2 w-2 rounded-full bg-[#D4AF37] shadow-[0_0_8px_#D4AF37]"></span>
                                    Mentor and apprenticeship progress
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="h-2 w-2 rounded-full bg-[#D4AF37] shadow-[0_0_8px_#D4AF37]"></span>
                                    Training, resources, events, and recognition
                                </div>
                            </div>
                        </div>
                    </aside>

                    <main class="flex items-center bg-[#0a0a0c] p-6 sm:p-8">
                        <div class="w-full">
                            <div class="mb-8">
                                <h2 class="text-2xl font-semibold text-white sm:text-3xl">Member Login</h2>
                                <p class="mt-2 text-sm text-slate-400">Secure portal access &middot; invitation-only membership</p>
                            </div>

                            <x-auth-session-status class="mb-5 rounded-2xl border border-emerald-400/30 bg-emerald-400/10 p-4 text-sm text-emerald-100" :status="session('status')" />

                            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                                @csrf

                                <div>
                                    <label for="email" class="block text-xs font-bold uppercase tracking-wide text-[#D4AF37]">Email</label>
                                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="member@example.com" class="mt-2 block w-full rounded-2xl border border-[#2a2a2e] bg-[#131316] px-4 py-3 text-sm text-slate-100 placeholder:text-slate-600 focus:border-[#D4AF37] focus:ring-[#D4AF37]">
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>

                                <div>
                                    <label for="password" class="block text-xs font-bold uppercase tracking-wide text-[#D4AF37]">Password</label>
                                    <input id="password" name="password" type="password" required autocomplete="current-password" placeholder="Enter password" class="mt-2 block w-full rounded-2xl border border-[#2a2a2e] bg-[#131316] px-4 py-3 text-sm text-slate-100 placeholder:text-slate-600 focus:border-[#D4AF37] focus:ring-[#D4AF37]">
                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                </div>

                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <label for="remember_me" class="inline-flex items-center gap-3 text-sm text-slate-300">
                                        <input id="remember_me" type="checkbox" class="rounded border-[#D4AF37]/40 bg-[#0a0a0c] text-[#D4AF37] focus:ring-[#D4AF37]" name="remember">
                                        <span>Remember me</span>
                                    </label>

                                    @if (Route::has('password.request'))
                                        <a class="text-sm font-semibold text-[#D4AF37] hover:text-[#F3D572] hover:underline" href="{{ route('password.request') }}">
                                            Forgot your password?
                                        </a>
                                    @endif
                                    @if (Route::has('verification.resend'))
                                        <a class="text-sm font-semibold text-[#D4AF37] hover:text-[#F3D572] hover:underline" href="{{ route('verification.resend', ['email' => old('email')]) }}">
                                            Resend verification email
                                        </a>
                                    @endif
                                </div>

                                <button type="submit" class="w-full rounded-full bg-gradient-to-r from-[#B8860B] via-[#D4AF37] to-[#F3D572] px-5 py-3 text-sm font-extrabold uppercase tracking-wider text-black shadow-lg transition hover:-translate-y-0.5 hover:shadow-[#D4AF37]/20">
                                    Log In
                                </button>

                                <div class="border-t border-[#D4AF37]/20 pt-5 text-center text-sm text-slate-400">
                                    Need access?
                                    <span class="font-semibold text-slate-200">Ask your sponsor for an invitation link.</span>
                                </div>
                            </form>
                        </div>
                    </main>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
