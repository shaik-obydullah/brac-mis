<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('returnee_microfinance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('returnee_id')->constrained()->cascadeOnDelete();
            $table->decimal('loan_amount', 12, 2);
            $table->text('loan_purpose')->nullable();
            $table->date('disbursement_date')->nullable();
            $table->text('repayment_schedule')->nullable();
            $table->enum('status', ['pending', 'active', 'repaid', 'defaulted'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('returnee_microfinance');
    }
};
