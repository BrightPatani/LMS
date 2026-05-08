<?php

namespace App\Services;

use App\Models\Submission;
use App\Repositories\SubmissionRepository;

class InstructorSubmissionService
{
    public function __construct(
        private SubmissionRepository $submissionRepository,
        private ActivityLogService   $activityLogService
    ) {}

    public function grade(
        int        $submissionId,
        int        $instructorId,
        array      $data
    ): array {
        $submission = Submission::with([
            'assignment.course',
            'student:id,name',
        ])->find($submissionId);

        if (!$submission) {
            return ['success' => false, 'message' => 'Submission not found.', 'code' => 404];
        }

        // Verify this submission belongs to a course owned by this instructor
        $courseOwnerId = $submission->assignment->course->user_id ?? null;

        if ($courseOwnerId !== $instructorId) {
            return ['success' => false, 'message' => 'You do not own this course.', 'code' => 403];
        }

        $graded = $this->submissionRepository->grade($submission, $data, $instructorId);

        $this->activityLogService->log(
            action:      'submission.graded',
            description: "Submission #{$submissionId} graded {$data['grade']}/100 by instructor #{$instructorId}",
            subject:     $graded,
            properties:  [
                'grade'         => $data['grade'],
                'instructor_id' => $instructorId,
                'student_id'    => $submission->user_id,
            ]
        );

        return ['success' => true, 'submission' => $graded];
    }
}