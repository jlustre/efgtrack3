<div class="space-y-6">
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 bg-gradient-to-r from-[#0B1F3A] to-[#132F55] px-6 py-5 text-white">
            <p class="text-xs font-semibold uppercase tracking-wide text-[#C8A24A]">Calculation settings</p>
            <h2 class="mt-1 text-xl font-semibold">Planning assumptions</h2>
            <p class="mt-2 max-w-3xl text-sm text-slate-200">
                Customize how income, production, and activity targets are computed in the Performance Planner, What-If calculator, and Success Blueprints.
            </p>
        </div>

        <form wire:submit="save" class="p-6">
            <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_280px]">
                <div class="space-y-8">
                    <section>
                        <h3 class="text-sm font-semibold uppercase tracking-wide text-[#8A6A1F]">Income & production</h3>
                        <div class="mt-4 grid gap-5 md:grid-cols-2">
                            <div>
                                <x-input-label for="income_commission_percent" :value="$settingsFields['income_commission_rate']['label'] ?? 'Income commission rate'" />
                                <div class="mt-1 flex items-center gap-2">
                                    <x-text-input id="income_commission_percent" type="number" step="0.1" min="1" max="100" wire:model.live="incomeCommissionPercent" class="block w-full" />
                                    <span class="text-sm font-semibold text-slate-500">%</span>
                                </div>
                                <p class="mt-1 text-xs leading-5 text-slate-500">{{ $settingsFields['income_commission_rate']['description'] ?? '' }}</p>
                                <x-input-error :messages="$errors->get('incomeCommissionPercent')" class="mt-1" />
                            </div>

                            <div>
                                <x-input-label for="avg_annual_premium" :value="$settingsFields['avg_annual_premium_per_application']['label'] ?? 'Average premium per application'" />
                                <div class="mt-1 flex items-center gap-2">
                                    <span class="text-sm font-semibold text-slate-500">$</span>
                                    <x-text-input id="avg_annual_premium" type="number" step="100" min="100" wire:model.live="avgAnnualPremiumPerApplication" class="block w-full" />
                                </div>
                                <p class="mt-1 text-xs leading-5 text-slate-500">{{ $settingsFields['avg_annual_premium_per_application']['description'] ?? '' }}</p>
                                <x-input-error :messages="$errors->get('avgAnnualPremiumPerApplication')" class="mt-1" />
                            </div>
                        </div>
                    </section>

                    <section>
                        <h3 class="text-sm font-semibold uppercase tracking-wide text-[#8A6A1F]">Calendar assumptions</h3>
                        <div class="mt-4 grid gap-5 md:grid-cols-3">
                            <div>
                                <x-input-label for="working_days" :value="$settingsFields['working_days_per_month']['label'] ?? 'Working days per month'" />
                                <x-text-input id="working_days" type="number" min="1" max="31" wire:model.live="workingDaysPerMonth" class="mt-1 block w-full" />
                                <p class="mt-1 text-xs leading-5 text-slate-500">{{ $settingsFields['working_days_per_month']['description'] ?? '' }}</p>
                                <x-input-error :messages="$errors->get('workingDaysPerMonth')" class="mt-1" />
                            </div>

                            <div>
                                <x-input-label for="working_weeks" :value="$settingsFields['working_weeks_per_year']['label'] ?? 'Working weeks per year'" />
                                <x-text-input id="working_weeks" type="number" min="1" max="52" wire:model.live="workingWeeksPerYear" class="mt-1 block w-full" />
                                <p class="mt-1 text-xs leading-5 text-slate-500">{{ $settingsFields['working_weeks_per_year']['description'] ?? '' }}</p>
                                <x-input-error :messages="$errors->get('workingWeeksPerYear')" class="mt-1" />
                            </div>

                            <div>
                                <x-input-label for="weeks_per_month" :value="$settingsFields['weeks_per_month']['label'] ?? 'Weeks per month'" />
                                <x-text-input id="weeks_per_month" type="number" step="0.01" min="1" max="5" wire:model.live="weeksPerMonth" class="mt-1 block w-full" />
                                <p class="mt-1 text-xs leading-5 text-slate-500">{{ $settingsFields['weeks_per_month']['description'] ?? '' }}</p>
                                <x-input-error :messages="$errors->get('weeksPerMonth')" class="mt-1" />
                            </div>
                        </div>
                    </section>

                    @foreach ($editableConversionRates as $funnelKey => $stages)
                        <section wire:key="conversion-section-{{ $funnelKey }}">
                            <h3 class="text-sm font-semibold uppercase tracking-wide text-[#8A6A1F]">{{ ucfirst($funnelKey) }} funnel conversion rates</h3>
                            <p class="mt-1 text-xs text-slate-500">Percent of the prior stage that advances to the next stage.</p>
                            <div class="mt-4 grid gap-4 md:grid-cols-2">
                                @foreach ($stages as $stage)
                                    @php($rateKey = "{$funnelKey}.{$stage['from']}.{$stage['to']}")
                                    <div wire:key="rate-{{ $rateKey }}">
                                        <x-input-label :for="'rate-'.$rateKey" :value="$stage['label']" />
                                        <div class="mt-1 flex items-center gap-2">
                                            <x-text-input :id="'rate-'.$rateKey" type="number" step="0.1" min="1" max="100" wire:model.live="conversionRates.{{ $rateKey }}" class="block w-full" />
                                            <span class="text-sm font-semibold text-slate-500">%</span>
                                        </div>
                                        <x-input-error :messages="$errors->get('conversionRates.'.$rateKey)" class="mt-1" />
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endforeach
                </div>

                <aside class="space-y-4">
                    <div class="rounded-xl border border-[#C8A24A]/40 bg-[#FFF9EA] p-5">
                        <h3 class="text-sm font-semibold text-[#0B1F3A]">Live preview</h3>
                        <p class="mt-1 text-xs text-slate-600">See how your assumptions translate from income to production.</p>

                        <div class="mt-4">
                            <x-input-label for="preview_income" value="Sample annual income" />
                            <div class="mt-1 flex items-center gap-2">
                                <span class="text-sm font-semibold text-slate-500">$</span>
                                <x-text-input id="preview_income" type="number" step="1000" min="1000" wire:model.live="previewIncome" class="block w-full" />
                            </div>
                        </div>

                        <dl class="mt-5 space-y-3 text-sm">
                            <div class="rounded-lg bg-white/80 px-3 py-2">
                                <dt class="text-xs uppercase text-slate-500">Income target</dt>
                                <dd class="text-lg font-bold text-[#0B1F3A]">${{ number_format($preview['income']) }}</dd>
                            </div>
                            <div class="rounded-lg bg-white/80 px-3 py-2">
                                <dt class="text-xs uppercase text-slate-500">Required production ({{ $preview['commission_percent'] }}%)</dt>
                                <dd class="text-lg font-bold text-[#0B1F3A]">${{ number_format($preview['production']) }}</dd>
                            </div>
                            <div class="rounded-lg bg-white/80 px-3 py-2">
                                <dt class="text-xs uppercase text-slate-500">Est. applications needed</dt>
                                <dd class="text-lg font-bold text-[#0B1F3A]">{{ number_format($preview['applications'], 1) }}</dd>
                            </div>
                        </dl>
                    </div>

                    @if ($hasCustomSettings)
                        <p class="text-xs text-slate-500">You are using custom calculation settings.</p>
                    @else
                        <p class="text-xs text-slate-500">Using system default assumptions.</p>
                    @endif
                </aside>
            </div>

            <div class="mt-8 flex flex-wrap gap-3 border-t border-slate-200 pt-6">
                <button type="submit" wire:loading.attr="disabled" class="rounded-lg bg-[#0B1F3A] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#132F55] disabled:opacity-60">
                    <span wire:loading.remove wire:target="save">Save settings</span>
                    <span wire:loading wire:target="save">Saving…</span>
                </button>
                <button type="button" wire:click="resetDefaults" wire:confirm="Restore all planning settings to system defaults?" class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Reset to defaults
                </button>
            </div>
        </form>
    </div>
</div>
