<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('resources')) {
            return;
        }

        if (! Schema::hasColumn('resources', 'category')) {
            Schema::table('resources', function (Blueprint $table): void {
                $table->string('category')->default('general');
            });
        }

        if (! Schema::hasColumn('resources', 'sort_order')) {
            Schema::table('resources', function (Blueprint $table): void {
                $table->unsignedInteger('sort_order')->default(0);
            });
        }

        if (! Schema::hasColumn('resources', 'is_featured')) {
            Schema::table('resources', function (Blueprint $table): void {
                $table->boolean('is_featured')->default(false);
            });
        }

        if (! Schema::hasColumn('resources', 'file_format')) {
            Schema::table('resources', function (Blueprint $table): void {
                $table->string('file_format', 20)->nullable();
            });
        }

        if (
            Schema::hasColumns('resources', ['type', 'category', 'is_published', 'sort_order'])
            && ! Schema::hasIndex('resources', 'resources_library_index')
        ) {
            Schema::table('resources', function (Blueprint $table): void {
                $table->index(['type', 'category', 'is_published', 'sort_order'], 'resources_library_index');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('resources')) {
            return;
        }

        if (Schema::hasIndex('resources', 'resources_library_index')) {
            Schema::table('resources', function (Blueprint $table): void {
                $table->dropIndex('resources_library_index');
            });
        }

        $columns = array_values(array_filter(
            ['category', 'sort_order', 'is_featured', 'file_format'],
            fn (string $column): bool => Schema::hasColumn('resources', $column),
        ));

        if ($columns !== []) {
            Schema::table('resources', function (Blueprint $table) use ($columns): void {
                $table->dropColumn($columns);
            });
        }
    }
};
