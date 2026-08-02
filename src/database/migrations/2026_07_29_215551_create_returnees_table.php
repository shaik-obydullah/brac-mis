<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('returnees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('migrant_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('beneficiary_id')->nullable()->constrained()->nullOnDelete();
            $table->date('return_date');
            $table->text('return_reason')->nullable();
            $table->foreignId('origin_country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->enum('current_status', ['assessed', 'planning', 'in_progress', 'completed', 'dropped'])->default('assessed');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('returnees');
    }
};
