<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'code' => 'required|string|max:50|unique:student_generations,code,' . $generationId,
        ];
    }
}
