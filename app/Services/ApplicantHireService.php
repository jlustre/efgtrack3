<?php

namespace App\Services;

use App\Events\ApplicantHired;
use App\Exceptions\ApplicantAlreadyHiredException;
use App\Models\User;
use Carbon\CarbonInterface;

class ApplicantHireService
{
    public function hire(User $user, User $hiredBy, ?CarbonInterface $hireDate = null): User
    {
        if ($user->isEmployee()) {
            throw new ApplicantAlreadyHiredException;
        }

        $hireDate ??= now();

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            ['recruited_at' => $hireDate->toDateString()],
        );

        $user = $user->fresh(['profile']);

        event(new ApplicantHired($user, $hiredBy));

        return $user;
    }
}
