<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\RegistrationInvitation;
use App\Models\Profile;
use App\Models\Rank;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(?string $code = null): View
    {
        abort_if($code === null, 403, 'Registration requires an invitation link from a current EFGTrack member.');

        $invitation = RegistrationInvitation::query()
            ->with('sponsor')
            ->where('code', $code)
            ->first();

        abort_if($invitation === null, 403, 'This invitation link is no longer available.');

        abort_unless($invitation->isAvailable(), 403, 'This invitation link is no longer available.');

        return view('auth.register', [
            'invitation' => $invitation,
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'registration_code' => ['required', 'string', 'exists:registration_invitations,code'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'efg_associate_id' => ['required', 'string', 'max:100', 'unique:profiles,efg_associate_id'],
            'city' => ['required', 'string', 'max:120'],
            'country' => ['required', 'string', 'in:Canada,United States,Philippines,Mexico'],
            'timezone' => ['required', 'string', 'max:120'],
            'sponsor_confirmed' => ['accepted'],
            'active_associate_confirmed' => ['accepted'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = DB::transaction(function () use ($request): User {
            $invitation = RegistrationInvitation::query()
                ->with('sponsor')
                ->where('code', $request->string('registration_code')->toString())
                ->lockForUpdate()
                ->firstOrFail();

            if (! $invitation->isAvailable()) {
                throw ValidationException::withMessages([
                    'registration_code' => 'This invitation link is no longer available.',
                ]);
            }

            if ($invitation->email && strtolower($invitation->email) !== strtolower($request->email)) {
                throw ValidationException::withMessages([
                    'email' => 'This invitation was issued for a different email address.',
                ]);
            }

            if (User::where('email', $request->email)->exists()) {
                throw ValidationException::withMessages([
                    'email' => 'This email is already registered as an EFGTrack member.',
                ]);
            }

            $fieldAssociateRank = Rank::where('code', 'FA')->first();

            $user = User::create([
                'name' => trim($request->first_name.' '.$request->last_name),
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'rank_id' => $fieldAssociateRank?->id,
                'team_id' => $invitation->sponsor?->team_id,
                'sponsor_id' => $invitation->sponsor_id,
                'is_active' => true,
                'joined_at' => now(),
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
                'is_online' => true,
            ]);

            $user->profile()->create([
                'efg_associate_id' => $request->string('efg_associate_id')->toString(),
                'city' => $request->string('city')->toString(),
                'country' => $request->string('country')->toString(),
                'timezone' => $request->string('timezone')->toString(),
                'is_efg_active_associate' => true,
            ]);

            if (Role::where('name', 'member')->exists()) {
                $user->assignRole('member');
            }

            $invitation->forceFill([
                'accepted_by' => $user->id,
                'accepted_at' => now(),
                'revoked_at' => now(),
                'uses_count' => $invitation->uses_count + 1,
            ])->save();

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
