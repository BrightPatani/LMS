<?php

namespace App\Http\Requests\File;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UploadThumbnailRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->role->value === 'instructor';   // Only allow authenticated users with the 'instructor' role to upload thumbnails     
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'thumbnail' => [
                'required',
                'image',
                'mimes:jpeg,png,gif', // Allowed image types
                'max:5120' // Max file size in KB (5 MB)
            ],  
        ];
    }
}
