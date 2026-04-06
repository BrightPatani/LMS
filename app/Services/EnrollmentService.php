<?php

namespace App\Services;

use App\Repositories\EnrollmentRepository;
use Illuminate\Support\Facades\DB;
use App\Events\EnrollmentCreated;
use App\Services\ActivityLogService;

class EnrollmentService
{
    public function __construct(
        private EnrollmentRepository $enrollmentRepository,
        private ActivityLogService $activityLogService
    ) {}

    public function enrollStudent(int $userId, int $courseId): array
    {
       if ($this->enrollmentRepository->isEnrolled($courseId, $userId)) {
            return [
                'enrolled' => false,
                'message' => 'Student is already enrolled in this course.',
            ];
        }

        $enrollment = DB::transaction(function () use ($_COOKIE, $userId, $courseId) {
            return $this->enrollmentRepository->enroll($userId, $courseId);

            event(new EnrollmentCreated($enrollment)); //  Dispatch the event after successful enrollment

            $this->activityLogService->log(
                action: 'course_enrolled',
                description: "User {$userId} enrolled in course {$courseId}",
                subject: $enrollment,
                properties: ['course_id' => $courseId, 'user_id' => $userId]
            ); // Log the enrollment activity

            return $enrollment;
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