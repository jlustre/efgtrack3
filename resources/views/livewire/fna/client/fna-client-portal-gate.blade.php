<div class="mx-auto max-w-md space-y-6">
    @if ($step === 1)
        <div>
            <h2 class="text-lg font-semibold text-[#0B1F3A]">Enter your security code</h2>
            <p class="mt-2 text-sm text-slate-600">Your financial advisor sent you a {{ config('fna.client_portal.security_code_length', 6) }}-digit code with your invite link.</p>
            @if ($invite?->recipient_user_id)
                <p class="mt-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-600">You may already have an EFGTrack member account. This secure form is separate from your member login.</p>
            @endif

            <form wire:submit="verifySecurityCode" class="mt-6 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700">Security code</label>
                    <input type="text" inputmode="numeric" maxlength="{{ config('fna.client_portal.security_code_length', 6) }}" wire:model="securityCode"
                        class="mt-1 block w-full rounded-lg border-slate-300 text-center text-2xl tracking-[0.4em] shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]" autocomplete="one-time-code">
                    @error('securityCode') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="w-full rounded-lg bg-[#0B1F3A] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#12345B]">Continue</button>
            </form>
        </div>
    @else
        <div>
            <h2 class="text-lg font-semibold text-[#0B1F3A]">Set up return access</h2>
            <p class="mt-2 text-sm text-slate-600">Create credentials so you can leave and come back later without the security code.</p>

            <form wire:submit="setupAccessCredentials" class="mt-6 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700">Email</label>
                    <input type="email" wire:model="accessEmail" class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
                    @error('accessEmail') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700">Phone</label>
                    <input type="tel" wire:model="accessPhone" class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
                    @error('accessPhone') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700">Last 4 digits of SSN</label>
                    <input type="password" inputmode="numeric" maxlength="4" wire:model="accessSsnLastFour"
                        class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]" autocomplete="off">
                    @error('accessSsnLastFour') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="w-full rounded-lg border border-[#C8A24A] bg-[#C8A24A] px-4 py-2.5 text-sm font-semibold text-[#0B1F3A] hover:bg-[#D8B85F]">Save &amp; continue to FNA</button>
            </form>
        </div>
    @endif
</div>
