<?php

namespace App\Repositories;

use App\Models\Assignment;

class AssignmentRepository
{
    public function create(array $data): Assignment
    {
        return Assignment::create($data); // this creates a new assignment record in the database using the provided data array, which should contain the necessary fields for creating an assignment (e.g., course_id, title, description, due_date).
    }

    public function findById(int $id): ?Assignment
    {
        return Assignment::with(['course', 'submissions'])->find($id); // this retrieves an assignment by its ID, along with its associated course and submissions, allowing you to access related data without additional queries.
    }
}