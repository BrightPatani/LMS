<?php

namespace App\Http\Requests\Instructor;

use Illuminate\Foundation\Http\FormRequest;

class GradeSubmissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role->value === 'instructor';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'grade' => 'required|integer|min:0|max:100',
            'feedback' => 'nullable|string|max:2000',
        ];
    }

    public function messages(): array 
    {
        return [
            'grade.min' => 'Grade cannot be below 0.',
            'grade.max' => 'Grade cannot be above 100.',
        ];
    }
}
