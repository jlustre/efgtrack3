<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('cfm_trainee_checklist_items')) {
            Schema::create('cfm_trainee_checklist_items', function (Blueprint $table): void {
                $table->id();
                $table->unsignedTinyInteger('phase_number');
                $table->string('phase_title');
                $table->string('phase_target')->nullable();
                $table->string('section_title');
                $table->string('title');
                $table->string('slug')->unique();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_required')->default(true);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();

                $table->index(['phase_number', 'sort_order']);
            });
        }

        if (! Schema::hasTable('cfm_trainee_checklist_progress')) {
            Schema::create('cfm_trainee_checklist_progress', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('mentor_assignment_id')
                    ->constrained('mentor_assignments', indexName: 'cfm_tc_prog_assignment_fk')
                    ->cascadeOnDelete();
                $table->foreignId('cfm_trainee_checklist_item_id')
                    ->constrained('cfm_trainee_checklist_items', indexName: 'cfm_tc_prog_item_fk')
                    ->cascadeOnDelete();
                $table->string('status')->default('not_started');
                $table->timestamp('completed_at')->nullable();
                $table->foreignId('completed_by')
                    ->nullable()
                    ->constrained('users', indexName: 'cfm_tc_prog_completed_by_fk')
                    ->nullOnDelete();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->unique(['mentor_assignment_id', 'cfm_trainee_checklist_item_id'], 'cfm_tc_progress_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cfm_trainee_checklist_progress');
        Schema::dropIfExists('cfm_trainee_checklist_items');
    }
};
