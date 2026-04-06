<?php

namespace App\Http\Requests\File;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;


class UploadLessonFileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->role->value === 'instructor'; // Only allow authenticated users with the 'instructor' role to upload files
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                'mimes:jpeg,png,gif,mp4,pdf,zip,doc,docx', // Allowed file types
                'max:10240' // Max file size in KB (10 MB)
            ],
        ];
    }
}
