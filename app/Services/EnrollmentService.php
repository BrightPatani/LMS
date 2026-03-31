<?php

namespace App\Services;

use App\Repositories\EnrollmentRepository;
use Illuminate\Support\Facades\DB;

class EnrollmentService
{
    public function __construct(
        private EnrollmentRepository $enrollmentRepository,
    ) {}

    public function enrollStudent(int $userId, int $courseId): array
    {
       if ($this->enrollmentRepository->isEnrolled($courseId, $userId)) {
            return [
                'enrolled' => false,
                'message' => 'User is already enrolled in this course.',
            ];
        }

        $enrollment = DB::transaction(function () use ($userId, $courseId) {
            return $this->enrollmentRepository->enroll($userId, $courseId);
        });

        return [
            'enrolled' => true,
            'message' => 'User enrolled in course successfully.',
            'enrollment' => $enrollment,
        ];

    } // this method checks if a user is already enrolled in a course, and if not, it enrolls the user in the course within a database transaction to ensure data integrity. It returns an array indicating whether the enrollment was successful and includes a message and the enrollment details if applicable.

    public function getUserCourses(int $userId)
    {
        return $this->enrollmentRepository->getUserCourses($userId); // this method retrieves the courses that a user is enrolled in by calling the getUserCourses method of the EnrollmentRepository, passing the user's ID as a parameter.
    }

    
}