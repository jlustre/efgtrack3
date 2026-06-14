<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prospect_funnels', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('key', 40)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['user_id', 'is_active']);
        });

        Schema::create('prospect_funnel_stages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('prospect_funnel_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pipeline_stage_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->decimal('conversion_weight', 5, 2)->default(0);
            $table->boolean('is_terminal')->default(false);
            $table->json('auto_task_template')->nullable();
            $table->timestamps();
            $table->unique(['prospect_funnel_id', 'slug']);
            $table->index(['prospect_funnel_id', 'sort_order']);
        });

        Schema::create('prospect_stage_history', function (Blueprint $table): void {
            $table->id();
            $table->foreignUlid('prospect_id')->constrained('prospects')->cascadeOnDelete();
            $table->foreignId('from_stage_id')->nullable()->constrained('pipeline_stages')->nullOnDelete();
            $table->foreignId('to_stage_id')->nullable()->constrained('pipeline_stages')->nullOnDelete();
            $table->foreignId('from_funnel_id')->nullable()->constrained('prospect_funnels')->nullOnDelete();
            $table->foreignId('to_funnel_id')->nullable()->constrained('prospect_funnels')->nullOnDelete();
            $table->foreignId('changed_by')->constrained('users')->cascadeOnDelete();
            $table->string('change_source', 40)->default('manual');
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['prospect_id', 'created_at']);
        });

        Schema::create('prospect_activities', function (Blueprint $table): void {
            $table->id();
            $table->foreignUlid('prospect_id')->constrained('prospects')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('activity_type', 60);
            $table->timestamp('occurred_at');
            $table->unsignedSmallInteger('duration_minutes')->nullable();
            $table->string('outcome', 80)->nullable();
            $table->text('notes')->nullable();
            $table->text('next_action')->nullable();
            $table->timestamp('next_follow_up_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['prospect_id', 'occurred_at']);
            $table->index(['user_id', 'occurred_at']);
        });

        Schema::table('prospects', function (Blueprint $table): void {
            $table->string('funnel_type', 20)->default('insurance')->after('pipeline_stage_id');
            $table->foreignId('prospect_funnel_id')->nullable()->after('funnel_type')->constrained()->nullOnDelete();
            $table->unsignedTinyInteger('interest_score')->nullable()->after('interest_level');
            $table->string('fna_status', 40)->nullable()->after('interest_score');
            $table->string('visibility_preset', 40)->default('private')->after('fna_status');
            $table->string('referral_source_name')->nullable()->after('visibility_preset');
            $table->string('campaign_name')->nullable()->after('referral_source_name');
            $table->string('event_name')->nullable()->after('campaign_name');
            $table->string('social_source', 120)->nullable()->after('event_name');
            $table->string('address_line_1')->nullable()->after('city');
            $table->string('postal_code', 40)->nullable()->after('state_province');
            $table->string('home_phone', 60)->nullable()->after('phone');
            $table->string('work_phone', 60)->nullable()->after('home_phone');
            $table->decimal('engagement_score', 5, 2)->default(0)->after('work_phone');
            $table->timestamp('last_activity_at')->nullable()->after('last_contacted_at');
            $table->index(['owner_id', 'prospect_funnel_id']);
            $table->index(['owner_id', 'funnel_type']);
        });
    }

    public function down(): void
    {
        Schema::table('prospects', function (Blueprint $table): void {
            $table->dropForeign(['prospect_funnel_id']);
            $table->dropColumn([
                'funnel_type',
                'prospect_funnel_id',
                'interest_score',
                'fna_status',
                'visibility_preset',
                'referral_source_name',
                'campaign_name',
                'event_name',
                'social_source',
                'address_line_1',
                'postal_code',
                'home_phone',
                'work_phone',
                'engagement_score',
                'last_activity_at',
            ]);
        });

        Schema::dropIfExists('prospect_activities');
        Schema::dropIfExists('prospect_stage_history');
        Schema::dropIfExists('prospect_funnel_stages');
        Schema::dropIfExists('prospect_funnels');
    }
};
