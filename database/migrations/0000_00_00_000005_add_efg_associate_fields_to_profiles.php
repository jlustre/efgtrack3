<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->string('efg_associate_id')->nullable()->unique()->after('license_number');
            $table->string('efg_invite_link')->nullable()->unique()->after('efg_associate_id');
            $table->boolean('is_efg_active_associate')->default(false)->after('efg_invite_link');
        });
    }

    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn(['efg_associate_id', 'efg_invite_link', 'is_efg_active_associate']);
        });
    }
};
