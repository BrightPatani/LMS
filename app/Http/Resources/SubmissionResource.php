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
            'assignment_id' => $this->assignment_id,
            'user_id' => $this->user_id,
            'content' => $this->content,
            'file' => new FileResource($this->whenLoaded('file')), // this includes the associated file of the submission in the response, using the FileResource to format it, but only if the 'file' relationship has been loaded.
            'file_path' => $this->file_path, // this includes the file path of the submission in the response, allowing clients to access the location of the submitted file if it exists.
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
