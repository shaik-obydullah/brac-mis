<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBranchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:branches,code',
            'district' => 'nullable|string|max:255',
            'division' => 'nullable|string|max:255',
            'status' => 'sometimes|in:active,inactive',
        ];
    }
}
