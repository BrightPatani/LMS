<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'instructor_id' => $this->instructor_id, // this includes the instructor_id in the response, which is a foreign key referencing the user who is the instructor of the course.
            'instructor' => new UserResource($this->whenLoaded('instructor')), // this loads the instructor relationship and returns it as a UserResource.
            'thumbnail' => $this->thumbnail,
            'lessons' => LessonResource::collection($this->whenLoaded('lessons')),
            'status' => $this->status,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
