<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('immediate_supports', 'user_id')) {
            Schema::table('immediate_supports', function (Blueprint $table) {
                $table->foreignId('user_id')->nullable()->after('support_type')->constrained()->nullOnDelete();
            });
        }

        foreach (['beneficiary_id', 'returnee_id'] as $column) {
            if (Schema::hasColumn('immediate_supports', $column)) {
                Schema::table('immediate_supports', function (Blueprint $table) use ($column) {
                    $table->dropConstrainedForeignId($column);
                });
            }
        }
    }

    public function down(): void
    {
        //
    }
};
