<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'code' => ['required', 'string', 'max:50', Rule::unique('study_programs', 'code')->ignore($programId)->withoutTrashed()],
            'faculty_id' => 'required|exists:faculties,id',
        ];
    }
}
