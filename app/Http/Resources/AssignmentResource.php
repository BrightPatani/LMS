<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssignmentResource extends JsonResource
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
            'course_id' => $this->course_id,
            'title' => $this->title,
            'description' => $this->description,
            'due_date' => $this->due_date->toDateTimeString(),
            'submissions' => SubmissionResource::collection($this->whenLoaded('submissions')), // this includes the associated submissions of the assignment in the response, using the SubmissionResource to format each submission, but only if the 'submissions' relationship has been loaded.
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
