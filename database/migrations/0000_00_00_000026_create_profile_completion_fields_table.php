<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profile_completion_fields', function (Blueprint $table) {
            $table->id();
            $table->string('field_key', 60);
            $table->string('label');
            $table->string('source', 20)->default('profile');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique('field_key');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profile_completion_fields');
    }
};
