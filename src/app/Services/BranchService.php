<?php

namespace App\Services;

use App\Models\Branch;
use Illuminate\Validation\ValidationException;

class BranchService
{
    public function validateHierarchy(string $type, ?int $parentId, ?int $ignoreId = null): void
    {
        if ($type === Branch::TYPE_HEAD_OFFICE) {
            if ($parentId !== null) {
                throw ValidationException::withMessages([
                    'parent_id' => 'A head office branch cannot have a parent branch.',
                ]);
            }

            $exists = Branch::where('type', Branch::TYPE_HEAD_OFFICE)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists();

            if ($exists) {
                throw ValidationException::withMessages([
                    'type' => 'A head office branch already exists.',
                ]);
            }

            return;
        }

        if ($parentId === null) {
            throw ValidationException::withMessages([
                'parent_id' => 'A parent branch is required for this branch type.',
            ]);
        }

        $parent = Branch::find($parentId);

        if ($type === Branch::TYPE_DISTRICT && $parent?->type !== Branch::TYPE_HEAD_OFFICE) {
            throw ValidationException::withMessages([
                'parent_id' => 'A district branch must be under the head office.',
            ]);
        }

        if ($type === Branch::TYPE_UPAZILA && $parent?->type !== Branch::TYPE_DISTRICT) {
            throw ValidationException::withMessages([
                'parent_id' => 'An upazila branch must be under a district branch.',
            ]);
        }
    }

    public function ensureTypeChangeAllowed(Branch $branch, string $type): void
    {
        if ($branch->type !== $type && $branch->children()->exists()) {
            throw ValidationException::withMessages([
                'type' => 'Cannot change the type of a branch that has child branches.',
            ]);
        }
    }
}
