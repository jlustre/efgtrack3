<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_onboarding_progress', function (Blueprint $table): void {
            $table->timestamp('submitted_at')->nullable()->after('status');
            $table->foreignId('reviewed_by')->nullable()->after('notes')->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
            $table->text('review_comments')->nullable()->after('reviewed_at');
        });
    }

    public function down(): void
    {
        Schema::table('user_onboarding_progress', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('reviewed_by');
            $table->dropColumn(['submitted_at', 'reviewed_at', 'review_comments']);
        });
    }
};
