<?php

namespace App\Repositories;

use App\Models\Course;
use Illuminate\Pagination\LengthAwarePaginator;

class CourseRepository
{
    public function paginate(int $perPage = 15) : LengthAwarePaginator
    {
        return Course::with(['instructor'])->latest()->paginate($perPage); // this retrieves courses along with their instructors, ordered by the most recent ones first, and paginates the results.
    }

    public function findById(int $id): ?Course
    {
        return Course::with(['instructor', 'lessons', 'assignments'])->find($id); // this retrieves a specific course by its ID, along with its instructor, lessons, and assignments.
    }

    public function create(array $data): Course
    {
        return Course::create($data); // this creates a new course record in the database using the provided data.
    }

    public function update(Course $course, array $data): Course
    {
        $course->update($data); // this updates an existing course record with the new data.
        return $course->fresh(); // this returns the updated course instance with the latest data from the database.
    }

    public function delete(Course $course): void
    {
        $course->delete(); // this deletes the specified course record from the database.
    }
}