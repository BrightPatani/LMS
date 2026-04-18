<?php

namespace App\Services;

use App\Repositories\EnrollmentRepository;
use Illuminate\Support\Facades\DB;
use App\Events\EnrollmentCreated;
use App\Services\ActivityLogService;
use Illuminate\Database\QueryException;

class EnrollmentService
{
    public function __construct(
        private EnrollmentRepository $enrollmentRepository,
        private ActivityLogService $activityLogService
    ) {}

    public function enrollStudent(int $userId, int $courseId): array
    {
        try {
            if ($this->enrollmentRepository->isEnrolled($userId, $courseId)) {
                return [
                    'enrolled' => false,
                    'message' => 'Student is already enrolled in this course.',
                    'enrollment' => null,
                ];
            }

            $enrollment = DB::transaction(function () use ($userId, $courseId) {

                $enrollment = $this->enrollmentRepository->enroll($userId, $courseId);

                event(new EnrollmentCreated($enrollment));

                $this->activityLogService->log(
                    action: 'course_enrolled',
                    description: "User {$userId} enrolled in course {$courseId}",
                    subject: $enrollment,
                    properties: ['course_id' => $courseId, 'user_id' => $userId]
                );

                return $enrollment;
            });

            return [
                'enrolled' => true,
                'message' => 'User enrolled successfully.',
                'enrollment' => $enrollment,
            ];

        } catch (QueryException $e) {

            // Catch duplicate entry error (MySQL 1062)
            if ($e->getCode() === '23000') {
                return [
                    'enrolled' => false,
                    'message' => 'Student is already enrolled in this course.',
                    
                ];
            }

            throw $e; // rethrow other errors
        }
    }
    public function getUserCourses(int $userId)
    {
        return $this->enrollmentRepository->getUserCourses($userId); // this method retrieves the courses that a user is enrolled in by calling the getUserCourses method of the EnrollmentRepository, passing the user's ID as a parameter.
    }

    
}