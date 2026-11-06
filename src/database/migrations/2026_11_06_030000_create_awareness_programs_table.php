<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('awareness_programs', function (Blueprint $table) {
            $table->id();
            $table->string('program_name');
            $table->string('division');
            $table->string('district');
            $table->string('upazila');
            $table->text('address')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('awareness_programs');
    }
};
