<?php

namespace App\Http\Requests\Finance;

use Illuminate\Foundation\Http\FormRequest;

class SizeChangeEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'faculty_id' => ['nullable', 'integer', 'exists:faculties,id'],
            'study_program_id' => ['nullable', 'integer', 'exists:study_programs,id'],
            'generation_id' => ['nullable', 'integer', 'exists:student_generations,id'],
            'student_level' => ['nullable', 'string', 'exists:student_levels,kode'],
            'max_changes' => ['required', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'allow_reedit' => ['boolean'],
            'baju_size_options_text' => ['nullable', 'string'],
            'sepatu_size_options_text' => ['nullable', 'string'],
        ];
    }
}
