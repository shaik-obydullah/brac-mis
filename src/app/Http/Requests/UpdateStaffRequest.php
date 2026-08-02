<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'sometimes|exists:users,id|unique:staff,user_id,' . $this->route('staff'),
            'employee_id' => 'sometimes|string|max:50|unique:staff,employee_id,' . $this->route('staff'),
            'designation' => 'sometimes|string|max:255',
            'branch_id' => 'nullable|exists:branches,id',
            'phone' => 'nullable|string|max:20',
        ];
    }
}
