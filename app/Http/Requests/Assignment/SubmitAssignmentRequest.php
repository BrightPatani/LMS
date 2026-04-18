<?php

namespace App\Http\Requests\Assignment;

use Illuminate\Foundation\Http\FormRequest;

class SubmitAssignmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'content' => 'required_without:file|nullable|string', // this rule allows the content field to be optional (nullable) but if it's not provided, then the file field must be present, ensuring that at least one of the two fields is provided when submitting an assignment.
            'file' => 'required_without:content|nullable|file|max:10240' // this rule allows the file field to be optional (nullable) but if it's not provided, then the content field must be present, ensuring that at least one of the two fields is provided when submitting an assignment. Additionally, it validates that if a file is uploaded, it must be a valid file and its size must not exceed 10MB (10240 KB), which helps prevent excessively large file uploads and ensures that the uploaded file is in a proper format.
        ];
    }
}
