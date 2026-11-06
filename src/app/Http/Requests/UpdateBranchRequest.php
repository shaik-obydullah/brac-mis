<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBranchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'code' => 'sometimes|string|max:50|unique:branches,code,'.$this->route('branch'),
            'type' => 'sometimes|in:head_office,district,upazila',
            'parent_id' => 'nullable|exists:branches,id',
            'division' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'upazila' => 'nullable|string|max:255',
            'status' => 'sometimes|in:active,inactive',
        ];
    }
}
