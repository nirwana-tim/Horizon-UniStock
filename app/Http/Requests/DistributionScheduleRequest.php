<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DistributionScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'period' => 'required|string|max:50',
            'semester' => 'required|string|in:Ganjil,Genap',
            'date' => 'required|date',
            'location' => 'required|string|max:255',
            'session' => 'required|string|max:100',
            'is_active' => 'boolean',
            'program_level_id' => 'nullable|integer|exists:program_levels,id',
            'faculty_id' => 'nullable|integer|exists:faculties,id',
            'study_program_id' => 'nullable|string',
            'item_ids' => 'nullable|array',
            'item_ids.*' => 'integer|exists:items,id',
        ];
    }
}
