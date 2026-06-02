<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('joined_at')->nullable()->after('is_active');
            $table->timestamp('last_login_at')->nullable()->after('joined_at');
            $table->string('last_login_ip', 45)->nullable()->after('last_login_at');
            $table->boolean('is_online')->default(false)->after('last_login_ip');
            $table->softDeletes();
        });

        foreach ([
            'profiles',
            'ranks',
            'teams',
            'licensing_steps',
            'user_licensing_progress',
            'onboarding_steps',
            'user_onboarding_progress',
            'training_categories',
            'training_modules',
            'training_lessons',
            'training_progress',
            'assessments',
            'questions',
            'answers',
            'assessment_attempts',
            'rank_requirements',
            'user_rank_progress',
            'resources',
            'events',
            'announcements',
            'badges',
            'registration_invitations',
            'mentor_assignments',
            'apprenticeship_programs',
            'apprenticeship_steps',
            'user_apprenticeship_progress',
            'mentor_notes',
            'cfm_training_modules',
            'cfm_training_progress',
            'cfm_certification_requests',
            'email_templates',
        ] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        foreach ([
            'email_templates',
            'cfm_certification_requests',
            'cfm_training_progress',
            'cfm_training_modules',
            'mentor_notes',
            'user_apprenticeship_progress',
            'apprenticeship_steps',
            'apprenticeship_programs',
            'mentor_assignments',
            'registration_invitations',
            'badges',
            'announcements',
            'events',
            'resources',
            'user_rank_progress',
            'rank_requirements',
            'assessment_attempts',
            'answers',
            'questions',
            'assessments',
            'training_progress',
            'training_lessons',
            'training_modules',
            'training_categories',
            'user_onboarding_progress',
            'onboarding_steps',
            'user_licensing_progress',
            'licensing_steps',
            'teams',
            'ranks',
            'profiles',
        ] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn([
                'joined_at',
                'last_login_at',
                'last_login_ip',
                'is_online',
            ]);
        });
    }
};
