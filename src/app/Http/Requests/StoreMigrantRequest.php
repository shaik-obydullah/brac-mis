<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMigrantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'beneficiary_id' => 'required|exists:beneficiaries,id',
            'destination_country_id' => 'nullable|exists:countries,id',
            'departure_date' => 'nullable|date',
            'return_date' => 'nullable|date|after_or_equal:departure_date',
            'recruitment_agent' => 'nullable|string|max:255',
            'recruitment_cost' => 'nullable|numeric|min:0',
            'monthly_salary' => 'nullable|numeric|min:0',
            'remittance_amount' => 'nullable|numeric|min:0',
            'remittance_frequency' => 'nullable|string|max:50',
            'employer_name' => 'nullable|string|max:255',
            'employer_contact' => 'nullable|string|max:50',
            'job_role' => 'nullable|string|max:255',
            'contract_duration' => 'nullable|string|max:100',
            'status' => 'sometimes|in:registered,pre_departure,deployed,returned,cancelled',
            'remarks' => 'nullable|string',
        ];
    }
}
