<?php

namespace App\Http\Requests\Finance;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EligibilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $eligibilityId = $this->route('eligibility')?->id;
        $isCreate = !$eligibilityId;

        return [
            'student_id' => [
                $isCreate ? 'required' : 'nullable',
                'integer',
                'exists:students,id',
                Rule::unique('eligibility_records', 'student_id')->ignore($eligibilityId),
            ],
            'is_eligible' => ['required', 'boolean'],
            'payment_status' => ['required', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'student_id.unique' => 'Mahasiswa ini sudah memiliki data kelayakan pembayaran.',
        ];
    }
}
