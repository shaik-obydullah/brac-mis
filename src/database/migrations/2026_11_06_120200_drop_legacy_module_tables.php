<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'returnee_follow_ups',
            'returnee_microfinance',
            'returnee_livelihood_support',
            'returnee_skill_assessments',
            'returnee_reintegration_plans',
            'returnees',
            'migrant_financial_records',
            'migrant_documents',
            'migrant_destinations',
            'migrants',
            'beneficiary_follow_ups',
            'beneficiary_interventions',
            'beneficiary_households',
            'beneficiary_documents',
            'beneficiaries',
            'follow_up_types',
            'intervention_types',
        ];

        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }
    }

    public function down(): void
    {
        //
    }
};
