<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProgramLevelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $levelId = $this->route('program_level')?->id;

        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:program_levels,code,' . $levelId,
        ];
    }
}
