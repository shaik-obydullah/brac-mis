<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id|unique:staff,user_id',
            'employee_id' => 'required|string|max:50|unique:staff,employee_id',
            'designation' => 'required|string|max:255',
            'branch_id' => 'nullable|exists:branches,id',
            'phone' => 'nullable|string|max:20',
        ];
    }
}
