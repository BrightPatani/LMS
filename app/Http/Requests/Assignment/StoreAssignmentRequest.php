<?php

namespace App\Http\Requests\Assignment;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssignmentRequest extends FormRequest
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
        'course_id' => 'required|exists:courses,id', // this rule ensures that the course_id is provided, is an integer, and exists in the courses table, preventing the creation of assignments for non-existent courses.
        'title' => 'required|string|max:255', // this rule ensures that the title of the assignment is provided, is a string, and does not exceed 255 characters in length, which helps maintain data integrity and prevents excessively long titles.
        'description' => 'required|string', // this rule allows the description to be optional (nullable) but if provided, it must be a string, giving flexibility to the user while ensuring that any provided description is in the correct format.
        'due_date' => 'required|date|after:today', // this rule ensures that the due_date is provided, is a valid date, and is set to a future date (after today), which helps prevent the creation of assignments with past due dates and ensures that the due date is meaningful for students.
        ];
    }
}
