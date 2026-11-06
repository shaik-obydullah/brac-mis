<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('support_follow_ups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('immediate_support_id')->constrained()->cascadeOnDelete();
            $table->date('follow_up_date');
            $table->string('method', 30)->default('phone_call');
            $table->string('status', 20)->default('scheduled');
            $table->text('outcome')->nullable();
            $table->text('notes')->nullable();
            $table->date('next_follow_up_date')->nullable();
            $table->foreignId('followed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('status');
            $table->index('follow_up_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_follow_ups');
    }
};
