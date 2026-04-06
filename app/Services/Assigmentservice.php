<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\Submission;
use App\Repositories\AssignmentRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class AssignmentService
{
   public function __construct(
        private AssignmentRepository $assigmentRepository
   ){}

   public function create(array $data): Assignment
   {
        return $this->assigmentRepository->create($data); // this creates a new assignment record in the database using the provided data array, which should contain the necessary fields for creating an assignment (e.g., course_id, title, description, due_date).  
   }

   public function submit(int $userId, Assignment $assignment, array $data): array
   {
        $alreadySubmitted = Submission::where('user_id', $userId)
            ->where('assignment_id', $assignment->id)
            ->exists();

            if ($alreadySubmitted) {
                return [
                    'submitted' => false,
                    'message' => 'You have already submitted this assignment.',
                ];
            }
        $submission = DB::transaction(function () use ($userId, $assignment, $data) {
            return Submission::create([
                'user_id' => $userId,
                'assignment_id' => $assignment->id,
                'content' => $data['content'] ?? null, // this sets the content of the submission to the value provided in the data array, or null if no content is provided.
                'status' => 'submitted', // this sets the status of the submission to 'submitted' when creating the submission record, indicating that the assignment has been submitted by the user.
                'submitted_at' => now(), // this sets the submitted_at timestamp to the current time when creating the submission record.
            ]);
        });

        return [
            'submitted' => true,
            'submission' => $submission,
        ];
   }

    public function getAll(): Collection
    {
        return Assignment::with('course')->get(); // this retrieves all assignments from the database, eager loading the associated course for each assignment to optimize database queries.
    }

    
}
