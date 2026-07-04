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
        return [
            'study_program_id' => 'required|integer|exists:study_programs,id',
            'program_level_id' => 'required|integer|exists:program_levels,id',
            'student_type' => 'required|string|in:freshman,continuing',
            'semester' => 'required|string|in:ganjil,genap',
            'description' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|integer|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ];
    }
}
