<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CfmTrainingModuleSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            [
                'title' => 'CFM Role And Responsibility Orientation',
                'description' => 'Understand the Certified Field Mentor role, apprentice expectations, boundaries, reporting cadence, and leadership responsibilities.',
                'is_required' => true,
            ],
            [
                'title' => 'Mentorship Standards And Ethics',
                'description' => 'Review professional standards, confidentiality, compliant communication, client-first conduct, and escalation expectations.',
                'is_required' => true,
            ],
            [
                'title' => 'FAP Coaching Framework',
                'description' => 'Learn how to guide apprentices through Field Apprenticeship Program milestones with structured coaching and documentation.',
                'is_required' => true,
            ],
            [
                'title' => 'Apprentice Readiness Assessment',
                'description' => 'Learn how to evaluate apprentice readiness, identify development gaps, and recommend next actions.',
                'is_required' => true,
            ],
            [
                'title' => 'Mentor Session Planning',
                'description' => 'Build effective mentor session agendas, follow-up rhythm, accountability actions, and milestone reviews.',
                'is_required' => true,
            ],
            [
                'title' => 'Field Observation And Debriefing',
                'description' => 'Practice observing field activity, giving feedback, debriefing client appointments, and reinforcing best practices.',
                'is_required' => true,
            ],
            [
                'title' => 'Licensing And Activity Boundaries',
                'description' => 'Understand how licensing status affects apprentice activities and when to involve sponsor, agency owner, or compliance support.',
                'is_required' => true,
            ],
            [
                'title' => 'Mentor Notes And Progress Documentation',
                'description' => 'Learn standards for mentor notes, progress updates, approval recommendations, and privacy-conscious documentation.',
                'is_required' => true,
            ],
            [
                'title' => 'Conflict Resolution And Escalation',
                'description' => 'Handle missed commitments, performance concerns, conduct issues, and escalation paths with professionalism.',
                'is_required' => true,
            ],
            [
                'title' => 'CFM Certification Review',
                'description' => 'Complete final review and confirm readiness to request Certified Field Mentor approval.',
                'is_required' => true,
            ],
            [
                'title' => 'Leadership Development Bonus Module',
                'description' => 'Optional leadership material for mentors preparing to support larger teams and future trainers.',
                'is_required' => false,
            ],
        ];

        $responsibleParties = [
            'CFM Role And Responsibility Orientation' => 'Self, SP, TR',
            'Mentorship Standards And Ethics' => 'Self, SP, TR',
            'FAP Coaching Framework' => 'Self, SP, TR',
            'Apprentice Readiness Assessment' => 'Self, SP, TR',
            'Mentor Session Planning' => 'Self, SP, TR',
            'Field Observation And Debriefing' => 'Self, SP, TR',
            'Licensing And Activity Boundaries' => 'Self, SP, TR',
            'Mentor Notes And Progress Documentation' => 'Self, SP, TR',
            'Conflict Resolution And Escalation' => 'Self, SP, TR',
            'CFM Certification Review' => 'SP, AO, TR',
            'Leadership Development Bonus Module' => 'Self, SP',
        ];

        $notifiedParties = [
            'CFM Role And Responsibility Orientation' => 'SP, TR',
            'Mentorship Standards And Ethics' => 'SP, TR',
            'FAP Coaching Framework' => 'SP, TR',
            'Apprentice Readiness Assessment' => 'SP, TR',
            'Mentor Session Planning' => 'SP, TR',
            'Field Observation And Debriefing' => 'SP, TR',
            'Licensing And Activity Boundaries' => 'SP, TR',
            'Mentor Notes And Progress Documentation' => 'SP, TR',
            'Conflict Resolution And Escalation' => 'SP, TR',
            'CFM Certification Review' => 'SP, AO, TR',
            'Leadership Development Bonus Module' => 'SP',
        ];

        foreach ($modules as $index => $module) {
            DB::table('cfm_training_modules')->updateOrInsert(
                ['title' => $module['title']],
                [
                    'description' => $module['description'],
                    'sort_order' => ($index + 1) * 10,
                    'responsible_parties' => $responsibleParties[$module['title']] ?? 'Self',
                    'notified_parties' => $notifiedParties[$module['title']] ?? null,
                    'is_active' => true,
                    'is_required' => $module['is_required'],
                    'deleted_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
