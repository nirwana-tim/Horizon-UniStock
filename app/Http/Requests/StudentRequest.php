<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $studentId = $this->route('student')?->id;

        return [
            'nim' => ['required', 'string', 'max:20', Rule::unique('students', 'nim')->ignore($studentId)],
            'name' => ['required', 'string', 'max:255'],
            'email_kampus' => ['required', 'email', 'max:255', Rule::unique('students', 'email_kampus')->ignore($studentId)],
            'email_pribadi' => ['nullable', 'email', 'max:255'],
            'study_program_id' => ['required', 'integer', 'exists:study_programs,id'],
            'generation_id' => ['nullable', 'integer', 'exists:student_generations,id'],
            'student_level' => ['required', 'string', 'exists:student_levels,kode'],
            'password' => ['nullable', 'string', 'min:8'],
        ];
    }
}
