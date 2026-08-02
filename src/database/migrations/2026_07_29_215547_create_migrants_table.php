<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('migrants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beneficiary_id')->nullable()->constrained()->nullOnDelete();
            $table->string('brac_id')->unique();
            $table->string('name');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->date('date_of_birth')->nullable();
            $table->string('nid_number')->nullable();
            $table->string('phone')->nullable();
            $table->string('passport_number')->nullable();
            $table->string('origin_district_id')->nullable();
            $table->string('origin_upazila_id')->nullable();
            $table->foreignId('destination_country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->string('destination_city')->nullable();
            $table->string('skill_level')->nullable();
            $table->string('education_level')->nullable();
            $table->string('occupation')->nullable();
            $table->enum('status', ['registered', 'pre_departure', 'deployed', 'returned', 'cancelled'])->default('registered');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('migrants');
    }
};
