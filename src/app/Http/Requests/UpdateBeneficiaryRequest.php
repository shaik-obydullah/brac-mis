<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBeneficiaryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'brac_id' => 'sometimes|string|unique:beneficiaries,brac_id,' . $this->route('beneficiary'),
            'name' => 'sometimes|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'gender' => 'sometimes|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'nid_number' => 'sometimes|string|max:50|unique:beneficiaries,nid_number,' . $this->route('beneficiary'),
            'phone' => 'nullable|string|max:20',
            'address_line_1' => 'nullable|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'occupation' => 'nullable|string|max:255',
            'monthly_income' => 'nullable|numeric|min:0',
            'family_size' => 'nullable|integer|min:1',
            'status' => 'sometimes|in:active,inactive,suspended',
            'branch_id' => 'nullable|exists:branches,id',
        ];
    }
}
