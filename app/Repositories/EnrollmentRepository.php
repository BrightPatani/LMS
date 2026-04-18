<?php

namespace App\Repositories;

use App\Models\Enrollment;
use App\Models\Course;

class EnrollmentRepository
{
    public function isEnrolled(int $userId, int $courseId): bool
    {
        return Enrollment::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->exists();
    }

    public function enroll(int $userId, int $courseId): Enrollment
    {
        return Enrollment::create([
            'user_id' => $userId,
            'course_id' => $courseId,
            'enrolled_at' => now(), // this sets the enrolled_at timestamp to the current time when creating the enrollment record.
        ]); // this creates a new enrollment record for the specified user ID and course ID, effectively enrolling the user in the course.
    }

    public function getUserCourses(int $userId)
    {
        return Course::whereHas('enrollments', fn($query) => $query->where('user_id', $userId))
            ->with('instructor') // this eager loads the instructor relationship for each course, allowing you to access the instructor's information without additional database queries.
            ->paginate(15); // this retrieves the courses that the user is enrolled in, paginating the results to show 15 courses per page.
    }
}