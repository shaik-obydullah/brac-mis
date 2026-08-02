<?php

namespace App\Jobs;

use App\Models\Beneficiary;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncBeneficiaryData implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public ?int $beneficiaryId = null,
    ) {}

    public function handle(): void
    {
        if ($this->beneficiaryId) {
            $beneficiaries = Beneficiary::where('id', $this->beneficiaryId)->lazy();
        } else {
            $beneficiaries = Beneficiary::lazy();
        }

        foreach ($beneficiaries as $beneficiary) {
            $migrantCount = $beneficiary->migrants()->count();
            $returneeCount = $beneficiary->returnees()->count();
            $followUpCount = $beneficiary->followUps()->count();

            if ($migrantCount > 0 && $beneficiary->status === 'active') {
                $hasActiveMigrant = $beneficiary->migrants()->whereIn('status', ['deployed', 'pre_departure'])->exists();
                if ($hasActiveMigrant) {
                    $beneficiary->status = 'migrated';
                    $beneficiary->save();
                }
            }

            $beneficiary->touch();
        }
    }
}
