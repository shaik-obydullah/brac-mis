<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('returnee_skill_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('returnee_id')->constrained()->cascadeOnDelete();
            $table->string('skill_name');
            $table->enum('proficiency_level', ['beginner', 'intermediate', 'advanced', 'expert']);
            $table->boolean('certification')->default(false);
            $table->string('assessed_by')->nullable();
            $table->date('assessed_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('returnee_skill_assessments');
    }
};
