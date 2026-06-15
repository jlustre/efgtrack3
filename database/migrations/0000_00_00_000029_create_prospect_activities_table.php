<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prospect_activities', function (Blueprint $table): void {
            $table->id();
            $table->ulid('prospect_id');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('activity_type')->index();
            $table->string('subject')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('occurred_at')->index();
            $table->string('outcome')->nullable();
            $table->text('next_action')->nullable();
            $table->timestamp('next_follow_up_at')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('prospect_id')->references('id')->on('prospects')->cascadeOnDelete();
            $table->index(['prospect_id', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prospect_activities');
    }
};
