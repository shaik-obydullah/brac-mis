<?php

namespace App\Services;

use App\Models\Beneficiary;
use Illuminate\Support\Str;

class BeneficiaryService
{
    public function generateBracId(): string
    {
        $prefix = 'BRAC-' . date('Y') . '-';
        $last = Beneficiary::where('brac_id', 'like', "{$prefix}%")
            ->orderBy('brac_id', 'desc')
            ->value('brac_id');

        $number = $last ? (int) Str::after($last, $prefix) + 1 : 1;

        return $prefix . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    public function registerBeneficiary(array $data): Beneficiary
    {
        if (!isset($data['brac_id']) || empty($data['brac_id'])) {
            $data['brac_id'] = $this->generateBracId();
        }

        $data['created_by'] = auth()->id();

        return Beneficiary::create($data);
    }

    public function searchBeneficiaries(array $filters)
    {
        $query = Beneficiary::with('branch', 'createdBy');

        if ($search = $filters['search'] ?? null) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('brac_id', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('nid_number', 'like', "%{$search}%");
            });
        }

        if ($status = $filters['status'] ?? null) {
            $query->where('status', $status);
        }

        if ($branchId = $filters['branch_id'] ?? null) {
            $query->where('branch_id', $branchId);
        }

        return $query->paginate($filters['per_page'] ?? 15);
    }
}
