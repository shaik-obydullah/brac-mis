<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('migrant_destinations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('migrant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('country_id')->constrained()->cascadeOnDelete();
            $table->string('city')->nullable();
            $table->string('employer_name')->nullable();
            $table->string('employer_contact')->nullable();
            $table->date('contract_start')->nullable();
            $table->date('contract_end')->nullable();
            $table->decimal('salary_amount', 12, 2)->nullable();
            $table->string('salary_currency', 3)->nullable();
            $table->enum('status', ['active', 'completed', 'terminated'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('migrant_destinations');
    }
};
