<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubmissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'assignment_id' => [
                    'id' => $this->assignment?->id,
                    'title' => $this->assignment?->title,
                    'due_date' => $this->assignment?->due_date?->toDateTimeString(),
                    'course' => $this->assignment?->course?->title,
                ],
            'user_id' => $this->user_id,
            'content' => $this->content,
            'file' => new FileResource($this->whenLoaded('file')), // this includes the associated file of the submission in the response, using the FileResource to format it, but only if the 'file' relationship has been loaded.
            'file_path' => $this->file_path, // this includes the file path of the submission in the response, allowing clients to access the location of the submitted file if it exists.
            'file_url' => $this->file_path ? asset('storage/' . $this->file_path) : null, // this generates a full URL to access the submitted file if the file path exists, using Laravel's asset helper to create a URL based on the storage path.
            'status' => $this->status_label,
            'grade' => $this->grade,
            'grade_label' => $this->grade !== null ? "{$this->grade}/100" : 'Not graded yet',
            'feedback' => $this->feedback,
            'graded_at' => $this->graded_at?->toDateTimeString(),
            'graded_by' => $this->gradedBy?->name,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
