<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('support_follow_ups', function (Blueprint $table) {
            $table->boolean('is_reintegrated')->default(false)->after('status');
            $table->timestamp('reintegrated_at')->nullable()->after('is_reintegrated');
        });
    }

    public function down(): void
    {
        Schema::table('support_follow_ups', function (Blueprint $table) {
            $table->dropColumn(['is_reintegrated', 'reintegrated_at']);
        });
    }
};
