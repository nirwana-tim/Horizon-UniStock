<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FacultyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $facultyId = $this->route('faculty')?->id;

        return [
            'name' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:50', Rule::unique('faculties', 'code')->ignore($facultyId)->withoutTrashed()],
        ];
    }
}
