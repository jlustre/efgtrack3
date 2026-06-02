<x-guest-layout>
    <x-auth-premium-shell
        headline="Create A New Password"
        intro="Set a fresh secure password and continue your EFGTrack journey with confidence."
        title="Reset Password"
        subtitle="Secure account recovery &middot; member access"
    >
        <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div>
                <label for="email" class="block text-xs font-bold uppercase tracking-wide text-[#D4AF37]">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username" placeholder="member@example.com" class="mt-2 block w-full rounded-2xl border border-[#2a2a2e] bg-[#131316] px-4 py-3 text-sm text-slate-100 placeholder:text-slate-600 focus:border-[#D4AF37] focus:ring-[#D4AF37]">
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <label for="password" class="block text-xs font-bold uppercase tracking-wide text-[#D4AF37]">Password</label>
                <input id="password" name="password" type="password" required autocomplete="new-password" placeholder="Create password" class="mt-2 block w-full rounded-2xl border border-[#2a2a2e] bg-[#131316] px-4 py-3 text-sm text-slate-100 placeholder:text-slate-600 focus:border-[#D4AF37] focus:ring-[#D4AF37]">
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wide text-[#D4AF37]">Confirm Password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" placeholder="Confirm password" class="mt-2 block w-full rounded-2xl border border-[#2a2a2e] bg-[#131316] px-4 py-3 text-sm text-slate-100 placeholder:text-slate-600 focus:border-[#D4AF37] focus:ring-[#D4AF37]">
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <button type="submit" class="w-full rounded-full bg-gradient-to-r from-[#B8860B] via-[#D4AF37] to-[#F3D572] px-5 py-3 text-sm font-extrabold uppercase tracking-wider text-black shadow-lg transition hover:-translate-y-0.5 hover:shadow-[#D4AF37]/20">
                Reset Password
            </button>
        </form>
    </x-auth-premium-shell>
</x-guest-layout>
