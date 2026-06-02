<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('onboarding_steps', function (Blueprint $table) {
            $table->string('country')->nullable()->after('is_required')->index();
        });
    }

    public function down(): void
    {
        Schema::table('onboarding_steps', function (Blueprint $table) {
            $table->dropColumn('country');
        });
    }
};
