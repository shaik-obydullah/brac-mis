<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->enum('type', ['head_office', 'district', 'upazila'])->default('district')->after('code');
            $table->foreignId('parent_id')->nullable()->after('type')->constrained('branches')->nullOnDelete();
        });

        $headOfficeId = DB::table('branches')->where('type', 'head_office')->value('id');

        if (! $headOfficeId) {
            $headOfficeId = DB::table('branches')->insertGetId([
                'name' => 'Head Office',
                'code' => 'HO-001',
                'type' => 'head_office',
                'parent_id' => null,
                'district' => 'Dhaka',
                'division' => 'Dhaka',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('branches')
            ->where('id', '!=', $headOfficeId)
            ->whereNull('parent_id')
            ->update(['type' => 'district', 'parent_id' => $headOfficeId]);
    }

    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropConstrainedForeignId('parent_id');
            $table->dropColumn('type');
        });
    }
};
