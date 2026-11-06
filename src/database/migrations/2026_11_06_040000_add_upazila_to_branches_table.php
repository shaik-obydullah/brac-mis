<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('branches', 'upazila')) {
            Schema::table('branches', function (Blueprint $table) {
                $table->string('upazila')->nullable()->after('district');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('branches', 'upazila')) {
            Schema::table('branches', function (Blueprint $table) {
                $table->dropColumn('upazila');
            });
        }
    }
};
