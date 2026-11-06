<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('participant_type', 30);
            $table->unsignedBigInteger('participant_id');
            $table->date('enrolled_at');
            $table->date('completed_at')->nullable();
            $table->string('status', 20)->default('enrolled');
            $table->decimal('score', 5, 2)->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['course_id', 'participant_type', 'participant_id'], 'course_participant_unique');
            $table->index(['participant_type', 'participant_id'], 'course_participant_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_enrollments');
    }
};
