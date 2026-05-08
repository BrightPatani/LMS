<?php

namespace App\Repositories;

use App\Models\Submission;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class SubmissionRepository
{
    public function getForStudent(int $userId, int $assignmentId): LengthAwarePaginator
    {
        return Submission::where('user_id', $userId)
            ->with([
                'assignment:id,title,due_date,course_id',
                'assignment.course:id,title',
                'gradedBy:id,name',
            ])->latest()
            ->paginate(15);

    }

    /**
     * Get all submissions for a specific student.
     */
    public function getAllForStudent(int $studentId): Collection
    {
        return Submission::where('user_id', $studentId)
            ->with(['lesson', 'course']) // Optional: Eager load related data
            ->latest()
            ->get();
    }

    public function findForStudent(int $submissionId, int $userId): ?Submission
    {
        return Submission::where('id', $submissionId)
            ->where('user_id', $userId)
            ->with([
                'assignment.course',
                'gradedBy:id,name',
            ])->first();
    }

    public function grade(Submission $submission, array $data, int $instructorId): Submission
    {
        $submission->update([
            'grade' => $data['grade'],
            'feedback' => $data['feedback'] ?? null,
            'graded_at' => now(),
            'graded_by' => $instructorId,
            'status' => 'graded',
        ]);

        return $submission->fresh(['gradedBy', 'student', 'assignment']);
    }
}