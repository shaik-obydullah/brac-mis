<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('immediate_supports', function (Blueprint $table) {
            $table->id();
            $table->string('support_type', 50);
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('recipient_name')->nullable();
            $table->string('recipient_phone', 20)->nullable();
            $table->date('support_date');
            $table->decimal('amount', 12, 2)->nullable();
            $table->string('status', 20)->default('provided');
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('provided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('support_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('immediate_supports');
    }
};
