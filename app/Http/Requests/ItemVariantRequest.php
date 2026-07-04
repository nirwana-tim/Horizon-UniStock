<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ItemVariantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $variantId = $this->route('variant')?->id;

        return [
            'size_id' => ['required', 'integer', 'exists:item_sizes,id'],
            'size' => ['required', 'string', 'max:10'],
            'sku' => ['nullable', 'string', 'max:50', Rule::unique('item_variants', 'sku')->ignore($variantId)],
            'weight' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
