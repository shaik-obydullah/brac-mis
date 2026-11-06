<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('awareness_program_outcomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('awareness_program_id')->constrained()->cascadeOnDelete();
            $table->string('participant_name');
            $table->string('participant_phone', 20)->nullable();
            $table->boolean('understood')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('understood');
        });

        if (Schema::hasColumn('awareness_programs', 'message_received')) {
            Schema::table('awareness_programs', function (Blueprint $table) {
                $table->dropColumn('message_received');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('awareness_program_outcomes');

        if (! Schema::hasColumn('awareness_programs', 'message_received')) {
            Schema::table('awareness_programs', function (Blueprint $table) {
                $table->boolean('message_received')->default(false)->after('address');
            });
        }
    }
};
