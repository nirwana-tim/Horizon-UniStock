<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StudentGenerationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $generationId = $this->route('student_generation')?->id;

        return [
            'name' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:50', Rule::unique('student_generations', 'code')->ignore($generationId)->withoutTrashed()],
        ];
    }
}
