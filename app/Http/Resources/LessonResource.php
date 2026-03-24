<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\FileResource;

class LessonResource extends JsonResource
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
            'course-id' => $this->course_id,
            'title' => $this->title,
            'content' => $this->content,
            'order' => $this->order,
            'files' => FileResource::collection($this->whenLoaded('files')), // this includes the associated files of the lesson in the response, using the FileResource to format each file, but only if the 'files' relationship has been loaded.
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
