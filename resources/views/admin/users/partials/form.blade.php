@props([
    'managedUser' => null,
    'roles',
    'ranks',
    'teams',
    'sponsors',
])

@php
    $selectedRole = old('role', $managedUser?->getRoleNames()->first() ?? 'member');
@endphp

<div class="grid gap-5 lg:grid-cols-2">
    <div>
        <label for="name" class="text-xs font-semibold uppercase tracking-wide text-slate-500">Name</label>
        <input id="name" name="name" value="{{ old('name', $managedUser?->name) }}" required class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div>
        <label for="email" class="text-xs font-semibold uppercase tracking-wide text-slate-500">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email', $managedUser?->email) }}" required class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>

    <div>
        <label for="password" class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $managedUser ? 'New Password' : 'Password' }}</label>
        <input id="password" name="password" type="password" @required(! $managedUser) class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
        <x-input-error :messages="$errors->get('password')" class="mt-2" />
    </div>

    <div>
        <label for="password_confirmation" class="text-xs font-semibold uppercase tracking-wide text-slate-500">Confirm Password</label>
        <input id="password_confirmation" name="password_confirmation" type="password" @required(! $managedUser) class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
    </div>

    <div>
        <label for="role" class="text-xs font-semibold uppercase tracking-wide text-slate-500">Role</label>
        <select id="role" name="role" required class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
            @foreach ($roles as $role)
                <option value="{{ $role }}" @selected($selectedRole === $role)>{{ $role }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('role')" class="mt-2" />
    </div>

    <div>
        <label for="rank_id" class="text-xs font-semibold uppercase tracking-wide text-slate-500">Rank</label>
        <select id="rank_id" name="rank_id" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
            <option value="">No rank</option>
            @foreach ($ranks as $rank)
                <option value="{{ $rank->id }}" @selected((string) old('rank_id', $managedUser?->rank_id) === (string) $rank->id)>{{ $rank->code }} - {{ $rank->name }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('rank_id')" class="mt-2" />
    </div>

    <div>
        <label for="team_id" class="text-xs font-semibold uppercase tracking-wide text-slate-500">Team</label>
        <select id="team_id" name="team_id" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
            <option value="">No team</option>
            @foreach ($teams as $team)
                <option value="{{ $team->id }}" @selected((string) old('team_id', $managedUser?->team_id) === (string) $team->id)>{{ $team->name }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('team_id')" class="mt-2" />
    </div>

    <div>
        <label for="sponsor_id" class="text-xs font-semibold uppercase tracking-wide text-slate-500">Sponsor</label>
        <select id="sponsor_id" name="sponsor_id" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
            <option value="">No sponsor</option>
            @foreach ($sponsors as $sponsor)
                <option value="{{ $sponsor->id }}" @selected((string) old('sponsor_id', $managedUser?->sponsor_id) === (string) $sponsor->id)>{{ $sponsor->name }} - {{ $sponsor->email }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('sponsor_id')" class="mt-2" />
    </div>

    <div>
        <label for="is_active" class="text-xs font-semibold uppercase tracking-wide text-slate-500">Account Status</label>
        <select id="is_active" name="is_active" required class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
            <option value="1" @selected((string) old('is_active', (int) ($managedUser?->is_active ?? true)) === '1')>Active</option>
            <option value="0" @selected((string) old('is_active', (int) ($managedUser?->is_active ?? true)) === '0')>Inactive</option>
        </select>
        <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
    </div>

    <div>
        <label for="joined_at" class="text-xs font-semibold uppercase tracking-wide text-slate-500">Joined Date</label>
        <input id="joined_at" name="joined_at" type="date" value="{{ old('joined_at', $managedUser?->joined_at?->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
        <x-input-error :messages="$errors->get('joined_at')" class="mt-2" />
    </div>
</div>
