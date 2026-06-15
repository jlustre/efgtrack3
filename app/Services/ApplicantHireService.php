<?php

namespace App\Services;

use App\Events\ApplicantHired;
use App\Exceptions\ApplicantAlreadyHiredException;
use App\Models\BpEmpAddress;
use App\Models\BpEmpCredential;
use App\Models\BpEmployee;
use App\Models\BpEmpDocument;
use App\Models\BpEmpPhone;
use App\Models\BpEmpTaxData;
use App\Models\BpJobData;
use App\Models\PeEmployee;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

class ApplicantHireService
{
    public function __construct(
        private readonly PreEmploymentSyncService $preEmploymentSync,
    ) {}

    public function hire(User $user, User $hiredBy, ?CarbonInterface $hireDate = null): BpEmployee
    {
        if ($user->isEmployee() || BpEmployee::query()->where('user_id', $user->id)->exists()) {
            throw new ApplicantAlreadyHiredException;
        }

        $hireDate ??= now();
        $peEmployee = $this->preEmploymentSync->sync($user);

        return DB::transaction(function () use ($user, $hiredBy, $hireDate, $peEmployee): BpEmployee {
            $bpEmployee = $this->copyEmployeeRecord($peEmployee, $hiredBy, $hireDate);
            $this->copyAddresses($peEmployee, $bpEmployee);
            $this->copyPhones($peEmployee, $bpEmployee);
            $this->copyJobData($peEmployee, $bpEmployee);
            $this->copyTaxData($peEmployee, $bpEmployee);
            $this->copyDocuments($peEmployee, $bpEmployee);
            $this->copyCredentials($peEmployee, $bpEmployee);

            $user->profile()->updateOrCreate(
                ['user_id' => $user->id],
                ['recruited_at' => $hireDate->toDateString()]
            );

            $peEmployee->update(['status' => 'hired']);

            $bpEmployee = $bpEmployee->fresh([
                'addresses',
                'phones',
                'jobData',
                'taxData',
                'documents',
                'credentials',
            ]);

            event(new ApplicantHired($user->fresh(['profile']), $bpEmployee, $hiredBy));

            return $bpEmployee;
        });
    }

    private function copyEmployeeRecord(PeEmployee $peEmployee, User $hiredBy, CarbonInterface $hireDate): BpEmployee
    {
        return BpEmployee::create([
            'user_id' => $peEmployee->user_id,
            'pe_employee_id' => $peEmployee->id,
            'first_name' => $peEmployee->first_name,
            'last_name' => $peEmployee->last_name,
            'email' => $peEmployee->email,
            'date_of_birth' => $peEmployee->date_of_birth,
            'license_number' => $peEmployee->license_number,
            'efg_associate_id' => $peEmployee->efg_associate_id,
            'efg_invite_link' => $peEmployee->efg_invite_link,
            'is_efg_active_associate' => $peEmployee->is_efg_active_associate,
            'bio' => $peEmployee->bio,
            'profile_photo_path' => $peEmployee->profile_photo_path,
            'best_contact_time' => $peEmployee->best_contact_time,
            'hire_date' => $hireDate->toDateString(),
            'hired_at' => now(),
            'hired_by' => $hiredBy->id,
            'status' => 'active',
        ]);
    }

    private function copyAddresses(PeEmployee $peEmployee, BpEmployee $bpEmployee): void
    {
        foreach ($peEmployee->addresses as $address) {
            BpEmpAddress::create([
                'bp_employee_id' => $bpEmployee->id,
                'pe_address_id' => $address->id,
                'type' => $address->type,
                'address_line_1' => $address->address_line_1,
                'address_line_2' => $address->address_line_2,
                'city' => $address->city,
                'country_id' => $address->country_id,
                'state_province_id' => $address->state_province_id,
                'postal_code' => $address->postal_code,
                'is_primary' => $address->is_primary,
            ]);
        }
    }

    private function copyPhones(PeEmployee $peEmployee, BpEmployee $bpEmployee): void
    {
        foreach ($peEmployee->phones as $phone) {
            BpEmpPhone::create([
                'bp_employee_id' => $bpEmployee->id,
                'pe_phone_id' => $phone->id,
                'type' => $phone->type,
                'phone_number' => $phone->phone_number,
                'extension' => $phone->extension,
                'is_primary' => $phone->is_primary,
            ]);
        }
    }

    private function copyJobData(PeEmployee $peEmployee, BpEmployee $bpEmployee): void
    {
        $jobData = $peEmployee->jobData;

        if ($jobData === null) {
            return;
        }

        BpJobData::create([
            'bp_employee_id' => $bpEmployee->id,
            'pe_job_data_id' => $jobData->id,
            'rank_id' => $jobData->rank_id,
            'team_id' => $jobData->team_id,
            'sponsor_id' => $jobData->sponsor_id,
            'mentor_id' => $jobData->mentor_id,
            'job_title' => $jobData->job_title,
            'start_date' => $jobData->start_date,
            'department' => $jobData->department,
            'employment_type' => $jobData->employment_type,
        ]);
    }

    private function copyTaxData(PeEmployee $peEmployee, BpEmployee $bpEmployee): void
    {
        $taxData = $peEmployee->taxData;

        if ($taxData === null) {
            return;
        }

        BpEmpTaxData::create([
            'bp_employee_id' => $bpEmployee->id,
            'pe_tax_data_id' => $taxData->id,
            'tax_id_type' => $taxData->tax_id_type,
            'tax_id_last_four' => $taxData->tax_id_last_four,
            'filing_status' => $taxData->filing_status,
            'exemptions' => $taxData->exemptions,
            'additional_withholding' => $taxData->additional_withholding,
            'w4_signed_at' => $taxData->w4_signed_at,
        ]);
    }

    private function copyDocuments(PeEmployee $peEmployee, BpEmployee $bpEmployee): void
    {
        foreach ($peEmployee->documents as $document) {
            BpEmpDocument::create([
                'bp_employee_id' => $bpEmployee->id,
                'pe_document_id' => $document->id,
                'document_type' => $document->document_type,
                'file_path' => $document->file_path,
                'original_filename' => $document->original_filename,
                'mime_type' => $document->mime_type,
                'status' => $document->status,
                'uploaded_at' => $document->uploaded_at,
                'expires_at' => $document->expires_at,
                'notes' => $document->notes,
            ]);
        }
    }

    private function copyCredentials(PeEmployee $peEmployee, BpEmployee $bpEmployee): void
    {
        foreach ($peEmployee->credentials as $credential) {
            BpEmpCredential::create([
                'bp_employee_id' => $bpEmployee->id,
                'pe_credential_id' => $credential->id,
                'credential_type' => $credential->credential_type,
                'credential_number' => $credential->credential_number,
                'issuing_authority' => $credential->issuing_authority,
                'jurisdiction_country_id' => $credential->jurisdiction_country_id,
                'jurisdiction_state_id' => $credential->jurisdiction_state_id,
                'issued_at' => $credential->issued_at,
                'expires_at' => $credential->expires_at,
                'status' => $credential->status,
            ]);
        }
    }
}
