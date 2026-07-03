<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ItemSizeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $sizeId = $this->route('item_size')?->id;

        return [
            'name' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:10', Rule::unique('item_sizes', 'code')->ignore($sizeId)],
            'sort_order' => 'nullable|integer|min:0',
        ];
    }
}
