<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('resources', function (Blueprint $table): void {
            $table->string('category')->default('general')->after('type');
            $table->unsignedInteger('sort_order')->default(0)->after('category');
            $table->boolean('is_featured')->default(false)->after('is_published');
            $table->string('file_format', 20)->nullable()->after('file_path');

            $table->index(['type', 'category', 'is_published', 'sort_order'], 'resources_library_index');
        });
    }

    public function down(): void
    {
        Schema::table('resources', function (Blueprint $table): void {
            $table->dropIndex('resources_library_index');
            $table->dropColumn(['category', 'sort_order', 'is_featured', 'file_format']);
        });
    }
};
