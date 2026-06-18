<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('checklist_types', function (Blueprint $table): void {
            $table->foreignId('prerequisite_checklist_type_id')
                ->nullable()
                ->after('max_complete_days')
                ->constrained('checklist_types')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('checklist_types', function (Blueprint $table): void {
            $table->dropForeign(['prerequisite_checklist_type_id']);
            $table->dropColumn('prerequisite_checklist_type_id');
        });
    }
};
