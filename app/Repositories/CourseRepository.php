<?php

namespace App\Repositories;

use App\Models\Course;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class CourseRepository
{
    private int $cacheTtl = 3600; // Cache for 1 hour

    public function paginate(int $perPage = 15) : LengthAwarePaginator
    {
        $page = request()->get('page', 1); // Get the current page from the request, default to 1 if not provided
        return Cache::tags(['courses'])->remember(
            "courses_page_{$page}_per_{$perPage}", // Cache key that includes the page number and items per page
            $this->cacheTtl,
            fn() => Course::with(['instructor'])
                ->withCount('enrollments') // Add a count of enrollments for each course
                ->orderBy('created_at', 'desc') // Order courses by creation date, newest first
                ->paginate($perPage) // Paginate the results with the specified number of items per page
        );
    }

    public function findById(int $id): ?Course
    {
        return Cache::tags(['courses'])->remember(
            "course_{$id}", // Cache key for the specific course
            $this->cacheTtl,
            fn() => Course::with(['instructor', 'lessons']) // Eager load the instructor and lessons relationships
                ->find($id) // Find the course by its ID
        );
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

    public function getPopular (int $limit = 10)
    {
        return Cache::tags(['courses'])->remember(
            "popular_courses_limit_{$limit}", // Cache key for popular courses with the specified limit
            $this->cacheTtl,
            fn() => Course::withCount('enrollments') // Add a count of enrollments for each course
                ->orderByDesc('enrollments_count') // Order courses by the number of enrollments, most popular first
                ->with(['instructor']) // Eager load the instructor relationship
                ->limit($limit) // Limit the results to the specified number of courses
                ->get() // Get the results as a collection
        );
    }

    public function delete(Course $course): void
    {
        $course->delete(); // this deletes the specified course record from the database.
    }

    public function clearCache(?int $courseId = null): void
    {
        if ($courseId) {
            Cache::forget("courses_{$courseId}"); // Clear cache for a specific course
        }

        Cache::tags(['courses'])->flush(); // Clear all course-related cache
    }
}