<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach ($this->tables() as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
                $table->timestamp('submitted_at')->nullable()->after('status');

                $afterColumn = Schema::hasColumn($tableName, 'notes') ? 'notes' : 'completed_at';

                $table->foreignId('reviewed_by')->nullable()->after($afterColumn)->constrained('users')->nullOnDelete();
                $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
                $table->text('review_comments')->nullable()->after('reviewed_at');
            });
        }
    }

    public function down(): void
    {
        foreach (array_reverse($this->tables()) as $tableName) {
            Schema::table($tableName, function (Blueprint $table): void {
                $table->dropConstrainedForeignId('reviewed_by');
                $table->dropColumn(['submitted_at', 'reviewed_at', 'review_comments']);
            });
        }
    }

    private function tables(): array
    {
        return [
            'user_licensing_progress',
            'user_apprenticeship_progress',
            'cfm_training_progress',
        ];
    }
};
