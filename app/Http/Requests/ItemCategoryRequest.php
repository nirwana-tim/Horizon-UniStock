<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ItemCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryId = $this->route('item_category')?->id;

        return [
            'label' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:3', Rule::unique('item_categories', 'code')->ignore($categoryId)],
        ];
    }
}
