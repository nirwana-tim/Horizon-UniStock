<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $itemId = $this->route('item')?->id;

        return [
            'code' => ['nullable', 'string', 'max:50', 'unique:items,code,' . $itemId],
            'name' => 'nullable|string|max:255',
            'category_id' => 'required|exists:item_categories,id',
            'type_id' => 'nullable|exists:item_types,id',
            'department_id' => 'nullable|exists:item_departments,id',
            'gender' => 'required|in:L,P,U',
            'unit' => 'required|string|max:20',
            'selling_price' => 'nullable|numeric|min:0',
            'hpp' => 'nullable|numeric|min:0',
            'size_ids' => 'required|array|min:1',
            'size_ids.*' => 'exists:item_sizes,id',
        ];
    }
}
