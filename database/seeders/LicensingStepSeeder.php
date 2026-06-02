<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LicensingStepSeeder extends Seeder
{
    public function run(): void
    {
        $steps = [
            [
                'title' => 'Confirm Licensing Jurisdiction',
                'description' => 'Confirm the country, province, state, or territory where the associate will begin licensing.',
                'is_required' => true,
            ],
            [
                'title' => 'Review Licensing Requirements',
                'description' => 'Review regulator requirements, pre-licensing education, exam expectations, timelines, fees, and required documents.',
                'is_required' => true,
            ],
            [
                'title' => 'Create Licensing Study Plan',
                'description' => 'Set study schedule, exam target date, weekly accountability rhythm, and support contact.',
                'is_required' => true,
            ],
            [
                'title' => 'Enroll In Required Licensing Course',
                'description' => 'Enroll in the required licensing course or approved education provider for the selected jurisdiction.',
                'is_required' => true,
            ],
            [
                'title' => 'Complete Pre-Licensing Education',
                'description' => 'Complete required coursework, practice modules, quizzes, and provider completion requirements.',
                'is_required' => true,
            ],
            [
                'title' => 'Submit Exam Registration',
                'description' => 'Register for the licensing exam and confirm the exam date, time, location, or online exam details.',
                'is_required' => true,
            ],
            [
                'title' => 'Complete Exam Prep Review',
                'description' => 'Complete final practice exams, weak-area review, and mentor check-in before the licensing exam.',
                'is_required' => true,
            ],
            [
                'title' => 'Pass Licensing Exam',
                'description' => 'Record successful exam completion and upload or note proof of passing when available.',
                'is_required' => true,
            ],
            [
                'title' => 'Submit License Application',
                'description' => 'Submit the license application, required forms, fees, disclosures, and supporting documents.',
                'is_required' => true,
            ],
            [
                'title' => 'Complete Background Or Compliance Requirements',
                'description' => 'Complete any background check, compliance questionnaire, E&O, or jurisdiction-specific suitability requirement.',
                'is_required' => true,
            ],
            [
                'title' => 'Receive License Approval',
                'description' => 'Confirm license approval and record license number or approval confirmation in the member profile.',
                'is_required' => true,
            ],
            [
                'title' => 'Complete Carrier Or Product Appointment Steps',
                'description' => 'Complete required appointment, contracting, product training, or access setup steps before field activity.',
                'is_required' => false,
            ],
            [
                'title' => 'Notify Sponsor And CFM Of Licensing Status',
                'description' => 'Notify the sponsor and assigned CFM that licensing status has changed and update the next field activity plan.',
                'is_required' => true,
            ],
        ];

        $responsibleParties = [
            'Confirm Licensing Jurisdiction' => 'Self, SP, CFM',
            'Review Licensing Requirements' => 'Self, SP, CFM',
            'Create Licensing Study Plan' => 'Self, SP, CFM',
            'Enroll In Required Licensing Course' => 'Self, SP, CFM',
            'Complete Pre-Licensing Education' => 'Self, SP, CFM',
            'Submit Exam Registration' => 'Self, SP, CFM',
            'Complete Exam Prep Review' => 'Self, SP, CFM',
            'Pass Licensing Exam' => 'Self, SP, CFM',
            'Submit License Application' => 'Self, SP, AO',
            'Complete Background Or Compliance Requirements' => 'Self, SP, AO',
            'Receive License Approval' => 'Self, SP, AO, CFM',
            'Complete Carrier Or Product Appointment Steps' => 'Self, SP, AO',
            'Notify Sponsor And CFM Of Licensing Status' => 'Self, SP, CFM',
        ];

        $notifiedParties = [
            'Confirm Licensing Jurisdiction' => 'SP, CFM',
            'Review Licensing Requirements' => 'SP, CFM',
            'Create Licensing Study Plan' => 'SP, CFM',
            'Enroll In Required Licensing Course' => 'SP, CFM',
            'Complete Pre-Licensing Education' => 'SP, CFM',
            'Submit Exam Registration' => 'SP, CFM',
            'Complete Exam Prep Review' => 'SP, CFM',
            'Pass Licensing Exam' => 'SP, CFM, AO',
            'Submit License Application' => 'SP, AO, CFM',
            'Complete Background Or Compliance Requirements' => 'SP, AO',
            'Receive License Approval' => 'SP, AO, CFM',
            'Complete Carrier Or Product Appointment Steps' => 'SP, AO',
            'Notify Sponsor And CFM Of Licensing Status' => 'SP, CFM',
        ];

        foreach ($steps as $index => $step) {
            DB::table('licensing_steps')->updateOrInsert(
                ['title' => $step['title']],
                [
                    'description' => $step['description'],
                    'sort_order' => ($index + 1) * 10,
                    'responsible_parties' => $responsibleParties[$step['title']] ?? 'Self',
                    'notified_parties' => $notifiedParties[$step['title']] ?? null,
                    'is_active' => true,
                    'is_required' => $step['is_required'],
                    'deleted_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
