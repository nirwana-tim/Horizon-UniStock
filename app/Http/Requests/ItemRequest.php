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
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:items,code,' . $itemId,
            'category_id' => 'required|exists:item_categories,id',
            'unit' => 'required|string|max:20',
            'selling_price' => 'required|numeric|min:0',
            'hpp' => 'required|numeric|min:0',
            'sizes' => 'nullable|array',
            'sizes.*.size' => 'nullable|string|max:10',
        ];
    }
}
