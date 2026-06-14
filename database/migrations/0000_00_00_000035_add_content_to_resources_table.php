<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('resources', function (Blueprint $table): void {
            $table->longText('content')->nullable()->after('description');
            $table->timestamp('pdf_generated_at')->nullable()->after('file_format');
        });
    }

    public function down(): void
    {
        Schema::table('resources', function (Blueprint $table): void {
            $table->dropColumn(['content', 'pdf_generated_at']);
        });
    }
};
