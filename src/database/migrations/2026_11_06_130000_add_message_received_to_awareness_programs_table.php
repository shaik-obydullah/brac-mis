<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('awareness_programs', 'message_received')) {
            Schema::table('awareness_programs', function (Blueprint $table) {
                $table->boolean('message_received')->default(false)->after('address');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('awareness_programs', 'message_received')) {
            Schema::table('awareness_programs', function (Blueprint $table) {
                $table->dropColumn('message_received');
            });
        }
    }
};
