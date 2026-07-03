<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockReceiveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vendor_id' => 'required|integer|exists:vendors,id',
            'receive_date' => 'required|date',
            'reference_number' => 'nullable|string|max:50|unique:stock_receives,reference_number,' . $this->route('stock_receive'),
            'notes' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|integer|exists:items,id',
            'items.*.variant_id' => 'required|integer|exists:item_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'nullable|numeric|min:0',
            'items.*.hpp' => 'nullable|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Minimal satu item harus ditambahkan.',
            'items.min' => 'Minimal satu item harus ditambahkan.',
            'items.*.item_id.required' => 'Item wajib dipilih.',
            'items.*.variant_id.required' => 'Varian ukuran wajib dipilih.',
            'items.*.quantity.required' => 'Jumlah barang wajib diisi.',
            'items.*.quantity.min' => 'Jumlah barang minimal 1.',
        ];
    }
}
