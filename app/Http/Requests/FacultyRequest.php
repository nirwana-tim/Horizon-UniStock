<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'code' => 'required|string|max:50|unique:facultas,code,' . $facultyId,
        ];
    }
}
