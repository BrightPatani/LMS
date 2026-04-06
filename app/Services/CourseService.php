<?php 

namespace App\Services;

use App\Models\Course;
use App\Repositories\CourseRepository;

class CourseService
{
    public function __construct(
        private CourseRepository $courseRepository,
    ) {}

    public function getAllCourses(): \Illuminate\Pagination\LengthAwarePaginator
    {
        return Course::with('instructor')->paginate(10); 
    }

    public function getCourseById(int $id): ?Course
    {
        return Course::with('instructor')->find($id); // this retrieves a specific course by its ID with the instructor relationship loaded.
    }

    public function createCourse(array $data, int $instructorId): Course
    {
        return $this->courseRepository->create([
            ...$data,
            'user_id' => $instructorId, // this creates a new course with the provided data and associates it with the instructor's user ID.
        ]);
         return $course->load(['instructor']); // this loads the instructor relationship for the newly created course before returning it.
    }

    public function getPopular(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->courseRepository->getPopular(); // this retrieves the most popular courses using the CourseRepository.
    }   

    public function updateCourse(Course $course, array $data): Course
    {
        return $this->courseRepository->update($course, $data); // this updates an existing course with the new data using the CourseRepository.
    }

    public function deleteCourse(course $course): void
    {
        $this->courseRepository->delete($course); // this deletes the specified course using the CourseRepository.
    }
}