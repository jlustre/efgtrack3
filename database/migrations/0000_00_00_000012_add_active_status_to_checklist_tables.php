<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach ($this->tables() as $tableName) {
            if (! Schema::hasColumn($tableName, 'is_active')) {
                Schema::table($tableName, function (Blueprint $table): void {
                    $table->boolean('is_active')->default(true)->after('notified_parties');
                });
            }
        }
    }

    public function down(): void
    {
        foreach (array_reverse($this->tables()) as $tableName) {
            if (Schema::hasColumn($tableName, 'is_active')) {
                Schema::table($tableName, function (Blueprint $table): void {
                    $table->dropColumn('is_active');
                });
            }
        }
    }

    private function tables(): array
    {
        return [
            'onboarding_steps',
            'licensing_steps',
            'apprenticeship_steps',
            'cfm_training_modules',
        ];
    }
};
