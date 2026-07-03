<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ItemDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $deptId = $this->route('item_department')?->id;

        return [
            'name' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:2', Rule::unique('item_departments', 'code')->ignore($deptId)],
        ];
    }
}
