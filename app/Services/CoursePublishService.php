<?php

namespace App\Services;

use App\Models\Course;

class CoursePublishService
{
    public function __construct(
        private ActivityLogService $activityLogService
    ) {}

    public function toggle(Course $course, int $instructorId): array
    {
        // Ownership check
        if ($course->user_id !== $instructorId) {
            return ['success' => false, 'message' => 'You do not own this course.', 'code' => 403];
        }

        // Prevent publishing an empty course
        if ($course->status === 'draft') {
            $lessonCount = $course->lessons()->count();

            if ($lessonCount === 0) {
                return [
                    'success' => false,
                    'message' => 'Cannot publish a course with no lessons. Add at least one lesson first.',
                    'code'    => 422,
                ];
            }
        }

        $newStatus = $course->status === 'published' ? 'draft' : 'published';

        $course->update(['status' => $newStatus]);

        // Bust the course cache
        \Illuminate\Support\Facades\Cache::tags(['courses'])->flush();

        $this->activityLogService->log(
            action:      $newStatus === 'published' ? 'course.published' : 'course.unpublished',
            description: "Course #{$course->id} {$newStatus} by instructor #{$instructorId}",
            subject:     $course,
            properties:  ['new_status' => $newStatus],
        );

        return [
            'success'    => true,
            'course_id'  => $course->id,
            'new_status' => $newStatus,
            'message'    => "Course has been {$newStatus}.",
        ];
    }

    public function setStatus(Course $course, string $status, int $instructorId): array
    {
        if ($course->user_id !== $instructorId) {
            return ['success' => false, 'message' => 'You do not own this course.', 'code' => 403];
        }

        if (!in_array($status, ['draft', 'published'])) {
            return ['success' => false, 'message' => 'Invalid status value.', 'code' => 422];
        }

        $course->update(['status' => $status]);

        \Illuminate\Support\Facades\Cache::tags(['courses'])->flush();

        return [
            'success'    => true,
            'course_id'  => $course->id,
            'new_status' => $status,
            'message'    => "Course status set to {$status}.",
        ];
    }
}