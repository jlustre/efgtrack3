<div class="grid gap-6 lg:grid-cols-5">
    <div class="space-y-4 lg:col-span-3">
        <form wire:submit="send" class="space-y-4 rounded-2xl border border-[#0B1F3A]/10 bg-white p-5 shadow-sm">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-[#0B1F3A]">Compose broadcast</h2>

            <div>
                <label for="broadcast-title" class="block text-sm font-medium text-slate-700">Title</label>
                <input
                    id="broadcast-title"
                    type="text"
                    wire:model="title"
                    class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-[#C8A24A] focus:outline-none focus:ring-1 focus:ring-[#C8A24A]"
                    placeholder="Important agency update"
                />
                @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="broadcast-body" class="block text-sm font-medium text-slate-700">Message</label>
                <textarea
                    id="broadcast-body"
                    wire:model="body"
                    rows="6"
                    class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-[#C8A24A] focus:outline-none focus:ring-1 focus:ring-[#C8A24A]"
                    placeholder="Write the broadcast message..."
                ></textarea>
                @error('body') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="broadcast-priority" class="block text-sm font-medium text-slate-700">Priority</label>
                    <select id="broadcast-priority" wire:model="priority" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        @foreach ($priorities as $key => $meta)
                            <option value="{{ $key }}">{{ $meta['label'] ?? ucfirst($key) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="broadcast-audience" class="block text-sm font-medium text-slate-700">Audience</label>
                    <select id="broadcast-audience" wire:model.live="audience_type" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        @foreach ($audienceTypes as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="rounded-lg border border-[#C8A24A]/30 bg-[#FFF9EA] px-4 py-3 text-sm text-[#8A6A1F]">
                Preview audience: <strong>{{ number_format($previewCount) }}</strong> {{ Str::plural('recipient', $previewCount) }}
            </div>

            <button type="submit" class="rounded-lg bg-[#0B1F3A] px-4 py-2 text-sm font-semibold text-[#C8A24A] transition hover:bg-[#132a4d]">
                Send broadcast
            </button>
        </form>
    </div>

    <div class="lg:col-span-2">
        <div class="rounded-2xl border border-[#0B1F3A]/10 bg-white p-5 shadow-sm">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-[#0B1F3A]">Broadcast history</h2>
            <ul class="mt-4 space-y-3 text-sm">
                @forelse ($recentBroadcasts as $broadcast)
                    <li class="rounded-lg border border-slate-100 bg-slate-50 px-3 py-3">
                        <p class="font-semibold text-[#0B1F3A]">{{ $broadcast->title }}</p>
                        <p class="mt-1 line-clamp-2 text-slate-600">{{ $broadcast->body }}</p>
                        <p class="mt-2 text-xs text-slate-500">
                            {{ $broadcast->sent_at?->format('M j, Y g:i A') }} · {{ number_format($broadcast->recipient_count) }} recipients
                        </p>
                    </li>
                @empty
                    <li class="text-slate-500">No broadcasts yet.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
