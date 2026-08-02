<?php

namespace App\Services;

use App\Models\Migrant;
use App\Models\MigrantDestination;
use App\Models\MigrantDocument;
use App\Models\MigrantFinancialRecord;
use Illuminate\Support\Facades\DB;

class MigrationService
{
    public function registerMigration(array $data): Migrant
    {
        return DB::transaction(function () use ($data) {
            return Migrant::create($data);
        });
    }

    public function deployMigrant(int $id, array $destinationData): Migrant
    {
        return DB::transaction(function () use ($id, $destinationData) {
            $migrant = Migrant::findOrFail($id);
            $migrant->destinations()->create($destinationData);
            $migrant->update(['status' => 'deployed']);

            return $migrant->fresh(['destinations', 'beneficiary', 'destinationCountry']);
        });
    }

    public function recordReturn(int $id, array $returnData): Migrant
    {
        return DB::transaction(function () use ($id, $returnData) {
            $migrant = Migrant::findOrFail($id);
            $migrant->update(array_merge($returnData, ['status' => 'returned']));

            return $migrant->fresh();
        });
    }

    public function getMigrationStats(): array
    {
        return [
            'total' => Migrant::count(),
            'registered' => Migrant::where('status', 'registered')->count(),
            'pre_departure' => Migrant::where('status', 'pre_departure')->count(),
            'deployed' => Migrant::where('status', 'deployed')->count(),
            'returned' => Migrant::where('status', 'returned')->count(),
            'cancelled' => Migrant::where('status', 'cancelled')->count(),
        ];
    }
}
