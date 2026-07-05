<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EntitlementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $entitlementId = $this->route('entitlement')?->id;

        return [
            'code' => [
                'required',
                'string',
                'max:50',
                "unique:entitlements,code,{$entitlementId}",
            ],
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|integer|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('items') && is_array($this->items)) {
            $filtered = [];
            foreach ($this->items as $item) {
                if (isset($item['checked']) && $item['checked'] == '1') {
                    $filtered[] = [
                        'item_id' => (int) $item['item_id'],
                        'quantity' => (int) ($item['quantity'] ?? 1),
                    ];
                }
            }
            $this->merge([
                'items' => $filtered,
            ]);
        }
    }
}
