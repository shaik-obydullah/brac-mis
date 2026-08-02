<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('migrant_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('migrant_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('file_path');
            $table->date('expiry_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('migrant_documents');
    }
};
