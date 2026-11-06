<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->string('upazila')->nullable()->after('district');
        });

        Schema::table('migrants', function (Blueprint $table) {
            $table->string('origin_division_id')->nullable()->after('passport_number');
        });

        Schema::table('returnees', function (Blueprint $table) {
            $table->string('division_id')->nullable()->after('origin_country_id');
            $table->string('district_id')->nullable()->after('division_id');
            $table->string('upazila_id')->nullable()->after('district_id');
        });
    }

    public function down(): void
    {
        Schema::table('returnees', function (Blueprint $table) {
            $table->dropColumn(['division_id', 'district_id', 'upazila_id']);
        });

        Schema::table('migrants', function (Blueprint $table) {
            $table->dropColumn('origin_division_id');
        });

        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn('upazila');
        });
    }
};
