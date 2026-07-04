<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ItemPriceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'item_id' => 'required|integer|exists:items,id',
            'selling_price' => 'required|numeric|min:0',
            'hpp' => 'required|numeric|min:0',
            'effective_date' => 'required|date',
        ];
    }
}
