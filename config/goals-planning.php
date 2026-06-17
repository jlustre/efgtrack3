<?php

return [

    'goal_types' => [
        'outcome' => 'Outcome Goal',
        'activity' => 'Activity Goal',
    ],

    'planning_types' => [
        'income' => [
            'label' => 'Annual Income Goal',
            'description' => 'Reverse-engineer production and daily activities from your income target.',
            'funnel' => 'income',
            'root_stage' => 'annual_income',
            'category_slug' => 'income',
            'measurement_type' => 'currency',
        ],
        'production' => [
            'label' => 'Production Goal',
            'description' => 'Plan applications, FNAs, presentations, and prospecting from premium targets.',
            'funnel' => 'production',
            'root_stage' => 'annual_production',
            'category_slug' => 'production',
            'measurement_type' => 'currency',
        ],
        'recruiting' => [
            'label' => 'Recruiting Goal',
            'description' => 'Build a recruiting funnel from your recruit target.',
            'funnel' => 'recruiting',
            'root_stage' => 'annual_recruits',
            'category_slug' => 'recruiting',
            'measurement_type' => 'number',
        ],
        'rank' => [
            'label' => 'Rank Advancement Goal',
            'description' => 'Roadmap production, recruiting, training, and leadership requirements.',
            'funnel' => 'rank',
            'root_stage' => 'target_rank',
            'category_slug' => 'rank_advancement',
            'measurement_type' => 'completion',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Funnel stages — ordered top (outcome) to bottom (activity)
    |--------------------------------------------------------------------------
    */
    'funnels' => [
        'income' => [
            ['key' => 'annual_income', 'label' => 'Annual Income', 'goal_type' => 'outcome', 'metric_key' => 'annual_income', 'measurement' => 'currency'],
            ['key' => 'annual_production', 'label' => 'Annual Production', 'goal_type' => 'outcome', 'metric_key' => 'annual_premium', 'measurement' => 'currency'],
            ['key' => 'monthly_production', 'label' => 'Monthly Production', 'goal_type' => 'outcome', 'metric_key' => 'monthly_premium', 'measurement' => 'currency'],
            ['key' => 'weekly_production', 'label' => 'Weekly Production', 'goal_type' => 'outcome', 'metric_key' => 'monthly_premium', 'measurement' => 'currency'],
            ['key' => 'applications', 'label' => 'Applications Submitted', 'goal_type' => 'activity', 'metric_key' => 'applications', 'measurement' => 'number'],
            ['key' => 'fnas', 'label' => 'FNAs Completed', 'goal_type' => 'activity', 'metric_key' => 'fna_completed', 'measurement' => 'number'],
            ['key' => 'presentations', 'label' => 'Presentations', 'goal_type' => 'activity', 'metric_key' => 'presentations', 'measurement' => 'number'],
            ['key' => 'appointments', 'label' => 'Appointments Held', 'goal_type' => 'activity', 'metric_key' => 'appointments', 'measurement' => 'number'],
            ['key' => 'invitations', 'label' => 'Invitations Sent', 'goal_type' => 'activity', 'metric_key' => 'invitations_sent', 'measurement' => 'number'],
            ['key' => 'prospect_contacts', 'label' => 'Prospect Conversations', 'goal_type' => 'activity', 'metric_key' => 'contacts', 'measurement' => 'number'],
            ['key' => 'daily_contacts', 'label' => 'Daily Prospecting Contacts', 'goal_type' => 'activity', 'metric_key' => 'contacts', 'measurement' => 'number'],
        ],
        'production' => [
            ['key' => 'annual_production', 'label' => 'Annual Production', 'goal_type' => 'outcome', 'metric_key' => 'annual_premium', 'measurement' => 'currency'],
            ['key' => 'monthly_production', 'label' => 'Monthly Production', 'goal_type' => 'outcome', 'metric_key' => 'monthly_premium', 'measurement' => 'currency'],
            ['key' => 'applications', 'label' => 'Applications', 'goal_type' => 'activity', 'metric_key' => 'applications', 'measurement' => 'number'],
            ['key' => 'fnas', 'label' => 'FNAs', 'goal_type' => 'activity', 'metric_key' => 'fna_completed', 'measurement' => 'number'],
            ['key' => 'presentations', 'label' => 'Presentations', 'goal_type' => 'activity', 'metric_key' => 'presentations', 'measurement' => 'number'],
            ['key' => 'appointments', 'label' => 'Appointments', 'goal_type' => 'activity', 'metric_key' => 'appointments', 'measurement' => 'number'],
            ['key' => 'invitations', 'label' => 'Invitations', 'goal_type' => 'activity', 'metric_key' => 'invitations_sent', 'measurement' => 'number'],
            ['key' => 'prospect_contacts', 'label' => 'Prospect Contacts', 'goal_type' => 'activity', 'metric_key' => 'contacts', 'measurement' => 'number'],
        ],
        'recruiting' => [
            ['key' => 'annual_recruits', 'label' => 'Annual Recruits', 'goal_type' => 'outcome', 'metric_key' => 'recruits', 'measurement' => 'number'],
            ['key' => 'monthly_recruits', 'label' => 'Monthly Recruits', 'goal_type' => 'outcome', 'metric_key' => 'recruits', 'measurement' => 'number'],
            ['key' => 'registrations', 'label' => 'Registrations', 'goal_type' => 'activity', 'metric_key' => 'registrations', 'measurement' => 'number'],
            ['key' => 'recruiting_presentations', 'label' => 'Opportunity Presentations', 'goal_type' => 'activity', 'metric_key' => 'presentations', 'measurement' => 'number'],
            ['key' => 'recruiting_appointments', 'label' => 'Recruiting Appointments', 'goal_type' => 'activity', 'metric_key' => 'appointments', 'measurement' => 'number'],
            ['key' => 'recruiting_invitations', 'label' => 'Recruiting Invitations', 'goal_type' => 'activity', 'metric_key' => 'invitations_sent', 'measurement' => 'number'],
            ['key' => 'recruiting_contacts', 'label' => 'Prospect Contacts', 'goal_type' => 'activity', 'metric_key' => 'contacts', 'measurement' => 'number'],
        ],
        'rank' => [
            ['key' => 'target_rank', 'label' => 'Target Rank', 'goal_type' => 'outcome', 'metric_key' => 'rank_requirements', 'measurement' => 'completion'],
            ['key' => 'rank_production', 'label' => 'Production Required', 'goal_type' => 'outcome', 'metric_key' => 'annual_premium', 'measurement' => 'currency'],
            ['key' => 'rank_recruits', 'label' => 'Recruits Required', 'goal_type' => 'outcome', 'metric_key' => 'team_recruits', 'measurement' => 'number'],
            ['key' => 'rank_licensing', 'label' => 'Licensing Progress', 'goal_type' => 'activity', 'metric_key' => 'licensing_completion', 'measurement' => 'percentage'],
            ['key' => 'rank_fap', 'label' => 'FAP Completion', 'goal_type' => 'activity', 'metric_key' => 'fap_completion', 'measurement' => 'percentage'],
            ['key' => 'rank_training', 'label' => 'Training Completion', 'goal_type' => 'activity', 'metric_key' => 'training_completion', 'measurement' => 'percentage'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default conversion rates (from_stage => to_stage => rate)
    | Rate = probability of advancing to next stage per period
    |--------------------------------------------------------------------------
    */
    'default_conversion_rates' => [
        'income' => [
            'annual_income' => ['annual_production' => 5.0],
            'annual_production' => ['monthly_production' => 12.0],
            'monthly_production' => ['weekly_production' => 4.33],
            'weekly_production' => ['applications' => 0.15],
            'applications' => ['fnas' => 0.85],
            'fnas' => ['presentations' => 0.75],
            'presentations' => ['appointments' => 0.60],
            'appointments' => ['invitations' => 0.50],
            'invitations' => ['prospect_contacts' => 0.40],
            'prospect_contacts' => ['daily_contacts' => 0.20],
        ],
        'production' => [
            'annual_production' => ['monthly_production' => 12.0],
            'monthly_production' => ['applications' => 0.12],
            'applications' => ['fnas' => 0.85],
            'fnas' => ['presentations' => 0.75],
            'presentations' => ['appointments' => 0.60],
            'appointments' => ['invitations' => 0.50],
            'invitations' => ['prospect_contacts' => 0.40],
        ],
        'recruiting' => [
            'annual_recruits' => ['monthly_recruits' => 12.0],
            'monthly_recruits' => ['registrations' => 0.50],
            'registrations' => ['recruiting_presentations' => 0.50],
            'recruiting_presentations' => ['recruiting_appointments' => 0.40],
            'recruiting_appointments' => ['recruiting_invitations' => 0.40],
            'recruiting_invitations' => ['recruiting_contacts' => 0.50],
        ],
        'rank' => [
            'target_rank' => ['rank_production' => 1.0],
            'rank_production' => ['rank_recruits' => 1.0],
            'rank_recruits' => ['rank_licensing' => 1.0],
            'rank_licensing' => ['rank_fap' => 1.0],
            'rank_fap' => ['rank_training' => 1.0],
        ],
    ],

    'planning_constants' => [
        'income_commission_rate' => 0.20,
        'avg_annual_premium_per_application' => 2500,
        'working_days_per_month' => 22,
        'working_weeks_per_year' => 48,
        'weeks_per_month' => 4.33,
    ],

    /*
    |--------------------------------------------------------------------------
    | User-editable planning settings (labels for settings UI)
    |--------------------------------------------------------------------------
    */
    'settings_fields' => [
        'income_commission_rate' => [
            'label' => 'Income commission rate',
            'description' => 'What percentage of your annual production is paid to you as income. Used to reverse-engineer production from an income target.',
            'type' => 'percent',
            'min' => 1,
            'max' => 100,
            'step' => 0.5,
        ],
        'avg_annual_premium_per_application' => [
            'label' => 'Average annual premium per application',
            'description' => 'Typical annual premium per submitted application. Used to estimate how many applications you need for a production target.',
            'type' => 'currency',
            'min' => 100,
            'max' => 1000000,
            'step' => 100,
        ],
        'working_days_per_month' => [
            'label' => 'Working days per month',
            'description' => 'Business days you plan to work each month when calculating daily activity targets.',
            'type' => 'integer',
            'min' => 1,
            'max' => 31,
            'step' => 1,
        ],
        'working_weeks_per_year' => [
            'label' => 'Working weeks per year',
            'description' => 'Weeks per year you actively work toward goals.',
            'type' => 'integer',
            'min' => 1,
            'max' => 52,
            'step' => 1,
        ],
        'weeks_per_month' => [
            'label' => 'Weeks per month',
            'description' => 'Average weeks in a month used to convert monthly targets into weekly targets.',
            'type' => 'decimal',
            'min' => 1,
            'max' => 5,
            'step' => 0.01,
        ],
    ],

    'editable_conversion_rates' => [
        'income' => [
            ['from' => 'applications', 'to' => 'fnas', 'label' => 'Applications → FNA completed'],
            ['from' => 'fnas', 'to' => 'presentations', 'label' => 'FNA → Presentation delivered'],
            ['from' => 'presentations', 'to' => 'appointments', 'label' => 'Presentation → Appointment held'],
            ['from' => 'appointments', 'to' => 'invitations', 'label' => 'Appointment → Invitation sent'],
            ['from' => 'invitations', 'to' => 'prospect_contacts', 'label' => 'Invitation → Prospect contact'],
        ],
        'production' => [
            ['from' => 'applications', 'to' => 'fnas', 'label' => 'Applications → FNA completed'],
            ['from' => 'fnas', 'to' => 'presentations', 'label' => 'FNA → Presentation delivered'],
            ['from' => 'presentations', 'to' => 'appointments', 'label' => 'Presentation → Appointment held'],
            ['from' => 'appointments', 'to' => 'invitations', 'label' => 'Appointment → Invitation sent'],
            ['from' => 'invitations', 'to' => 'prospect_contacts', 'label' => 'Invitation → Prospect contact'],
        ],
        'recruiting' => [
            ['from' => 'monthly_recruits', 'to' => 'registrations', 'label' => 'Monthly recruit target → Registration'],
            ['from' => 'registrations', 'to' => 'recruiting_presentations', 'label' => 'Registration → Opportunity presentation'],
            ['from' => 'recruiting_presentations', 'to' => 'recruiting_appointments', 'label' => 'Presentation → Recruiting appointment'],
            ['from' => 'recruiting_appointments', 'to' => 'recruiting_invitations', 'label' => 'Appointment → Recruiting invitation'],
            ['from' => 'recruiting_invitations', 'to' => 'recruiting_contacts', 'label' => 'Invitation → Prospect contact'],
        ],
    ],

    'activity_scorecard' => [
        'new_prospects' => ['label' => 'New Prospects', 'metric_key' => 'new_prospects'],
        'contacts' => ['label' => 'Calls / Contacts', 'metric_key' => 'contacts'],
        'followups_completed' => ['label' => 'Follow-Ups', 'metric_key' => 'followups_completed'],
        'appointments' => ['label' => 'Appointments', 'metric_key' => 'appointments'],
        'presentations' => ['label' => 'Presentations', 'metric_key' => 'presentations'],
        'fna_completed' => ['label' => 'FNAs', 'metric_key' => 'fna_completed'],
        'applications' => ['label' => 'Applications', 'metric_key' => 'applications'],
        'invitations_sent' => ['label' => 'Invitations', 'metric_key' => 'invitations_sent'],
        'recruits' => ['label' => 'Recruits', 'metric_key' => 'recruits'],
    ],

    'fap_activities' => [
        'observation_sessions' => 'Observation Sessions',
        'practice_presentations' => 'Practice Presentations',
        'mentor_meetings' => 'Mentor Meetings',
        'prospecting_activities' => 'Prospecting Activities',
        'training_completion' => 'Training Completion',
        'resource_reviews' => 'Resource Reviews',
    ],

    'alert_rules' => [
        'no_prospecting_days' => 7,
        'no_presentations_days' => 14,
        'no_fna_days' => 14,
        'no_followups_days' => 7,
        'pace_behind_percent' => 80,
    ],

    'rank_requirements' => [
        'SM' => ['production' => 50000, 'recruits' => 2, 'licensing' => 100, 'fap' => 100, 'training' => 80],
        'ED' => ['production' => 150000, 'recruits' => 5, 'licensing' => 100, 'fap' => 100, 'training' => 100],
        'SED' => ['production' => 300000, 'recruits' => 10, 'licensing' => 100, 'fap' => 100, 'training' => 100],
    ],

];
