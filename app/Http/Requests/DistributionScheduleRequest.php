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
            'student_level' => $this->input('student_level') ?: null,
            'study_program_id' => $this->input('study_program_id') === 'all' ? null : $this->input('study_program_id'),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'period' => 'nullable|string|max:7',
            'student_level' => 'nullable|string|exists:student_levels,kode',
            'date' => 'required|date',
            'location' => 'required|string|max:255',
            'session' => 'nullable|string|max:100',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after_or_equal:start_time',
            'is_active' => 'boolean',
            'faculty_id' => 'nullable|integer|exists:faculties,id',
            'study_program_id' => 'nullable|integer|exists:study_programs,id',
            'item_ids' => 'nullable|array',
            'item_ids.*' => 'integer|exists:items,id',
        ];
    }
}
