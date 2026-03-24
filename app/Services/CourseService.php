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
        return $this->courseRepository->paginate(); // this retrieves a paginated list of courses using the CourseRepository.}
    }

    public function getCourseById(int $id): ?Course
    {
        return $this->courseRepository->findById($id); // this retrieves a specific course by its ID using the CourseRepository.
    }

    public function createCourse(array $data, int $instructorId): Course
    {
        return $this->courseRepository->create([
            ...$data,
            'user_id' => $instructorId, // this creates a new course with the provided data and associates it with the instructor's user ID.
        ]);
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