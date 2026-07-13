<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DistributionScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'student_type' => $this->input('student_type') ?: null,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'semester' => 'required|string|in:Ganjil,Genap',
            'student_type' => 'nullable|string|in:freshman,continuing',
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
