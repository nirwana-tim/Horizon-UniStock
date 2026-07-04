<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ItemTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $typeId = $this->route('item_type')?->id;

        return [
            'label' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:3', Rule::unique('item_types', 'code')->ignore($typeId)],
        ];
    }
}
