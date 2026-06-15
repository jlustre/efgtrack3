<?php

namespace App\Services;

use App\Models\PeEmpAddress;
use App\Models\PeEmpCredential;
use App\Models\PeEmployee;
use App\Models\PeEmpPhone;
use App\Models\PeJobData;
use App\Models\Profile;
use App\Models\User;

class PreEmploymentSyncService
{
    public function sync(User $user): PeEmployee
    {
        $user->loadMissing('profile');

        $profile = $user->profile ?? $user->profile()->create([]);
        [$firstName, $lastName] = $this->splitName($user->name);

        $peEmployee = PeEmployee::query()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $user->email,
                'license_number' => $profile->license_number,
                'efg_associate_id' => $profile->efg_associate_id,
                'efg_invite_link' => $profile->efg_invite_link,
                'is_efg_active_associate' => (bool) $profile->is_efg_active_associate,
                'bio' => $profile->bio,
                'profile_photo_path' => $profile->profile_photo_path,
                'best_contact_time' => $profile->best_contact_time,
                'status' => $user->isEmployee() ? 'hired' : 'pending',
            ]
        );

        $this->syncPrimaryAddress($peEmployee, $profile);
        $this->syncPrimaryPhone($peEmployee, $profile);
        $this->syncJobData($peEmployee, $user);
        $this->syncLicenseCredential($peEmployee, $profile);

        return $peEmployee->fresh([
            'addresses',
            'phones',
            'jobData',
            'taxData',
            'documents',
            'credentials',
        ]);
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function splitName(string $name): array
    {
        $trimmed = trim($name);

        if ($trimmed === '') {
            return ['', ''];
        }

        $parts = preg_split('/\s+/', $trimmed, 2) ?: [];

        return [
            $parts[0],
            $parts[1] ?? '',
        ];
    }

    private function syncPrimaryAddress(PeEmployee $peEmployee, Profile $profile): void
    {
        if (
            blank($profile->city)
            && blank($profile->country_id)
            && blank($profile->state_province_id)
        ) {
            return;
        }

        PeEmpAddress::query()->updateOrCreate(
            [
                'pe_employee_id' => $peEmployee->id,
                'type' => 'home',
                'is_primary' => true,
            ],
            [
                'city' => $profile->city,
                'country_id' => $profile->country_id,
                'state_province_id' => $profile->state_province_id,
            ]
        );
    }

    private function syncPrimaryPhone(PeEmployee $peEmployee, Profile $profile): void
    {
        if (blank($profile->phone)) {
            return;
        }

        PeEmpPhone::query()->updateOrCreate(
            [
                'pe_employee_id' => $peEmployee->id,
                'type' => 'mobile',
                'is_primary' => true,
            ],
            [
                'phone_number' => $profile->phone,
            ]
        );
    }

    private function syncJobData(PeEmployee $peEmployee, User $user): void
    {
        PeJobData::query()->updateOrCreate(
            ['pe_employee_id' => $peEmployee->id],
            [
                'rank_id' => $user->rank_id,
                'team_id' => $user->team_id,
                'sponsor_id' => $user->sponsor_id,
                'mentor_id' => $user->mentor_id,
                'job_title' => $user->rank?->name,
                'start_date' => $user->joined_at?->toDateString(),
                'employment_type' => 'independent_contractor',
            ]
        );
    }

    private function syncLicenseCredential(PeEmployee $peEmployee, Profile $profile): void
    {
        if (blank($profile->license_number)) {
            return;
        }

        PeEmpCredential::query()->updateOrCreate(
            [
                'pe_employee_id' => $peEmployee->id,
                'credential_type' => 'license',
            ],
            [
                'credential_number' => $profile->license_number,
                'jurisdiction_country_id' => $profile->country_id,
                'jurisdiction_state_id' => $profile->state_province_id,
                'status' => 'active',
            ]
        );
    }
}
