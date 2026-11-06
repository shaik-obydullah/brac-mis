<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('immediate_supports', 'paid_from')) {
            Schema::table('immediate_supports', function (Blueprint $table) {
                $table->string('paid_from', 20)->default('cash')->after('amount');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('immediate_supports', 'paid_from')) {
            Schema::table('immediate_supports', function (Blueprint $table) {
                $table->dropColumn('paid_from');
            });
        }
    }
};
