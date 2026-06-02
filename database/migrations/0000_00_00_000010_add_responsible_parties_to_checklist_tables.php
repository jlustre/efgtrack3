<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach ($this->tables() as $tableName) {
            Schema::table($tableName, function (Blueprint $table): void {
                $table->string('responsible_parties')->default('Self')->after('sort_order');
            });
        }
    }

    public function down(): void
    {
        foreach (array_reverse($this->tables()) as $tableName) {
            Schema::table($tableName, function (Blueprint $table): void {
                $table->dropColumn('responsible_parties');
            });
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
