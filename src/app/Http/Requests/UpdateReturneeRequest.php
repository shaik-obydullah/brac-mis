<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReturneeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'migrant_id' => 'nullable|exists:migrants,id',
            'beneficiary_id' => 'sometimes|exists:beneficiaries,id',
            'origin_country_id' => 'nullable|exists:countries,id',
            'return_date' => 'nullable|date',
            'reason_for_return' => 'nullable|string',
            'health_status' => 'nullable|string|max:255',
            'psychological_status' => 'nullable|string|max:255',
            'reintegration_readiness' => 'nullable|string|max:100',
            'assistance_received' => 'nullable|string',
            'status' => 'sometimes|in:registered,assessing,assisting,reintegrated,closed',
            'remarks' => 'nullable|string',
        ];
    }
}
