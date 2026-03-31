<?php

namespace App\Repositories;

use App\Models\Submission;
use Illuminate\Support\Facades\DB;

class SubmissionRepository
{
    public function submit(int $userId, int $assignmentId, array $data): array
    {
        $alreadySubmitted = Submission::where('user_id', $userId)
            ->where('assignment_id', $assignmentId)
            ->exists();

        if ($alreadySubmitted) {
            return [
                'submitted' => false,
                'message' => 'User has already submitted this assignment.',
            ];
        }

        $submission = DB::transaction(function () use ($userId, $assignmentId, $data) {
            return Submission::create([
                'user_id' => $userId,
                'assignment_id' => $assignmentId,
                'content' => $data['content'] ?? null, // this sets the content of the submission to the value provided in the data array, or null if no content is provided.
                'submitted_at' => now(), // this sets the submitted_at timestamp to the current time when creating the submission record.
            ]);
        });

        return [
            'submitted' => true,
            'message' => 'Submission created successfully.',
            'data' => $submission,
        ];

    } 
}