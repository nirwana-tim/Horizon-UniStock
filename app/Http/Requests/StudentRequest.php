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
            'program_level_id' => ['required', 'integer', 'exists:program_levels,id'],
            'student_type' => ['required', 'string', 'in:year_1_sem_1,year_1_sem_2,year_2_sem_3,year_2_sem_4,continuing'],
            'password' => ['nullable', 'string', 'min:8'],
        ];
    }
}
