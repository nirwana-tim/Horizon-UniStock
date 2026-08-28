<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ItemPriceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $itemPriceId = $this->route('item_price')?->id;

        return [
            'item_id' => 'required|integer|exists:items,id',
            'selling_price' => 'required|numeric|min:0',
            'hpp' => 'required|numeric|min:0',
            'effective_date' => [
                'required',
                'date',
                Rule::unique('item_prices', 'effective_date')
                    ->where(fn ($q) => $q->where('item_id', $this->input('item_id')))
                    ->ignore($itemPriceId),
            ],
        ];
    }
}
