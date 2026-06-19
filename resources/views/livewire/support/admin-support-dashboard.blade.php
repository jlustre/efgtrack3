<div class="space-y-4">
    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
        <div class="rounded-xl border border-zinc-800 bg-zinc-900 p-4">
            <p class="text-xs uppercase text-zinc-500">Open tickets</p>
            <p class="mt-1 text-2xl font-bold text-zinc-100">{{ $metrics['open_tickets'] }}</p>
        </div>
        <div class="rounded-xl border border-red-500/30 bg-red-500/10 p-4">
            <p class="text-xs uppercase text-red-300">Urgent SLA breach</p>
            <p class="mt-1 text-2xl font-bold text-red-200 animate-pulse">{{ $metrics['urgent_breaches'] }}</p>
        </div>
        <div class="rounded-xl border border-amber-500/30 bg-amber-500/10 p-4">
            <p class="text-xs uppercase text-amber-300">At risk</p>
            <p class="mt-1 text-2xl font-bold text-amber-200">{{ $metrics['at_risk'] }}</p>
        </div>
        <div class="rounded-xl border border-zinc-800 bg-zinc-900 p-4">
            <p class="text-xs uppercase text-zinc-500">Awaiting user</p>
            <p class="mt-1 text-2xl font-bold text-zinc-100">{{ $metrics['awaiting_user'] }}</p>
        </div>
        <div class="rounded-xl border border-zinc-800 bg-zinc-900 p-4">
            <p class="text-xs uppercase text-zinc-500">Wishlist submitted</p>
            <p class="mt-1 text-2xl font-bold text-zinc-100">{{ $metrics['wishlist_submitted'] }}</p>
        </div>
        <div class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 p-4">
            <p class="text-xs uppercase text-emerald-300">In development</p>
            <p class="mt-1 text-2xl font-bold text-emerald-200">{{ $metrics['wishlist_in_development'] }}</p>
        </div>
    </div>

    <div class="rounded-2xl border border-zinc-800 bg-zinc-900 p-4">
        <div class="grid gap-3 lg:grid-cols-5">
            <input type="search" wire:model.live.debounce.300ms="global_search" placeholder="Search tickets…" class="rounded-lg border border-zinc-700 bg-zinc-950 p-3 text-sm text-zinc-100 lg:col-span-2">
            <select wire:model.live="filter_status" class="rounded-lg border border-zinc-700 bg-zinc-950 p-3 text-sm text-zinc-100">
                <option value="">All statuses</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status->id }}">{{ $status->name }}</option>
                @endforeach
            </select>
            <select wire:model.live="filter_module" class="rounded-lg border border-zinc-700 bg-zinc-950 p-3 text-sm text-zinc-100">
                <option value="">All modules</option>
                @foreach ($modules as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
            <select wire:model.live="filter_urgency" class="rounded-lg border border-zinc-700 bg-zinc-950 p-3 text-sm text-zinc-100">
                <option value="">All urgency</option>
                @foreach ($urgencyLevels as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <label class="mt-3 flex items-center gap-2 text-sm text-zinc-300">
            <input type="checkbox" wire:model.live="toggle_sla_breach_only" class="rounded border-zinc-600 text-amber-500 focus:ring-amber-500/50">
            SLA breach only
        </label>
    </div>

    <div class="overflow-hidden rounded-2xl border border-zinc-800 bg-zinc-900">
        <table class="min-w-full text-sm">
            <thead class="bg-zinc-950 text-left text-xs uppercase tracking-wide text-zinc-500">
                <tr>
                    <th class="px-4 py-3">Ticket</th>
                    <th class="px-4 py-3">Subject</th>
                    <th class="px-4 py-3">User</th>
                    <th class="px-4 py-3">Priority</th>
                    <th class="px-4 py-3">SLA</th>
                    <th class="px-4 py-3">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-800 text-zinc-300">
                @foreach ($tickets as $ticket)
                    <tr wire:click="openTicket({{ $ticket->id }})" class="cursor-pointer hover:bg-zinc-800/60">
                        <td class="px-4 py-3 font-mono text-amber-300">{{ $ticket->ticket_number }}</td>
                        <td class="px-4 py-3">{{ $ticket->subject }}</td>
                        <td class="px-4 py-3">{{ $ticket->user?->name }}</td>
                        <td class="px-4 py-3">{{ $ticket->computed_priority_score ?? $ticket->priority_score }}</td>
                        <td class="px-4 py-3">
                            @if ($ticket->sla_status === 'overdue')
                                <span class="rounded-full bg-red-500/20 px-2 py-0.5 text-xs font-semibold text-red-300 animate-pulse">Overdue</span>
                            @elseif ($ticket->sla_status === 'at_risk')
                                <span class="rounded-full bg-amber-500/20 px-2 py-0.5 text-xs font-semibold text-amber-300 animate-pulse">At risk</span>
                            @else
                                <span class="text-zinc-500">On track</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ $ticket->status?->name }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="border-t border-zinc-800 p-4">{{ $tickets->links() }}</div>
    </div>

    @if ($selectedTicket)
        <div class="fixed inset-0 z-[200] flex items-start justify-center overflow-y-auto bg-black/70 p-4" wire:click.self="closeTicketModal">
            <div class="w-full max-w-3xl rounded-2xl border border-zinc-700 bg-zinc-900 p-6 shadow-2xl">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="font-mono text-amber-400">{{ $selectedTicket->ticket_number }}</p>
                        <h3 class="mt-1 text-lg font-semibold text-zinc-100">{{ $selectedTicket->subject }}</h3>
                        <p class="mt-2 text-sm text-zinc-400">{{ $selectedTicket->description }}</p>
                    </div>
                    <button type="button" wire:click="closeTicketModal" class="text-zinc-400 hover:text-zinc-200">✕</button>
                </div>

                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    <div>
                        <label class="text-xs uppercase text-zinc-500">Assign to</label>
                        <select wire:model="assign_to" class="mt-1 w-full rounded-lg border border-zinc-700 bg-zinc-950 p-3 text-sm text-zinc-100">
                            <option value="">Unassigned</option>
                            @foreach ($agents as $agent)
                                <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs uppercase text-zinc-500">Status</label>
                        <select wire:model="new_status_id" class="mt-1 w-full rounded-lg border border-zinc-700 bg-zinc-950 p-3 text-sm text-zinc-100">
                            @foreach ($statuses as $status)
                                <option value="{{ $status->id }}">{{ $status->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="text-xs uppercase text-zinc-500">Reply to user</label>
                    <textarea wire:model="agent_reply" rows="3" class="mt-1 w-full rounded-lg border border-zinc-700 bg-zinc-950 p-3 text-sm text-zinc-100"></textarea>
                </div>
                <div class="mt-3">
                    <label class="text-xs uppercase text-zinc-500">Internal note</label>
                    <textarea wire:model="internal_note" rows="2" class="mt-1 w-full rounded-lg border border-zinc-700 bg-zinc-950 p-3 text-sm text-zinc-100"></textarea>
                </div>

                <div class="mt-4 flex flex-wrap gap-2">
                    <button type="button" wire:click="saveTicketActions" class="rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-black hover:bg-amber-600">Save changes</button>
                    <button type="button" wire:click="convertToWishlist" class="rounded-lg border border-zinc-600 px-4 py-2 text-sm font-semibold text-zinc-300 hover:bg-zinc-800">Convert to wishlist</button>
                </div>
            </div>
        </div>
    @endif
</div>
