<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudyProgramRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $programId = $this->route('study_program')?->id;

        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:study_programs,code,' . $programId,
            'faculty_id' => 'required|exists:faculties,id',
        ];
    }
}
