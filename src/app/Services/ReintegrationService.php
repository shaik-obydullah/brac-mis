<?php

namespace App\Services;

use App\Models\Returnee;
use App\Models\ReturneeReintegrationPlan;
use App\Models\ReturneeSkillAssessment;
use App\Models\ReturneeLivelihoodSupport;
use Illuminate\Support\Facades\DB;

class ReintegrationService
{
    public function registerReturnee(array $data): Returnee
    {
        return DB::transaction(function () use ($data) {
            return Returnee::create($data);
        });
    }

    public function createReintegrationPlan(int $returneeId, array $data): ReturneeReintegrationPlan
    {
        $returnee = Returnee::findOrFail($returneeId);

        return $returnee->reintegrationPlans()->create($data);
    }

    public function assessSkills(int $returneeId, array $data): ReturneeSkillAssessment
    {
        $returnee = Returnee::findOrFail($returneeId);

        return $returnee->skillAssessments()->create($data);
    }

    public function provideLivelihoodSupport(int $returneeId, array $data): ReturneeLivelihoodSupport
    {
        $returnee = Returnee::findOrFail($returneeId);

        return $returnee->livelihoodSupport()->create($data);
    }

    public function getReintegrationStats(): array
    {
        return [
            'total' => Returnee::count(),
            'registered' => Returnee::where('status', 'registered')->count(),
            'assessing' => Returnee::where('status', 'assessing')->count(),
            'assisting' => Returnee::where('status', 'assisting')->count(),
            'reintegrated' => Returnee::where('status', 'reintegrated')->count(),
            'closed' => Returnee::where('status', 'closed')->count(),
        ];
    }
}
