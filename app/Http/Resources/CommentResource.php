<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
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
        'body' => $this->body,
        'author' => new UserResource($this->whenLoaded('author')), // this loads the author relationship and returns it as a UserResource, providing details about the user who made the comment.
        'commentable_type' => $this->commentable_type,  // this includes the type of the entity being commented on (course, lesson, or assignment) in the response.
        'commentable_id' => $this->commentable_id, // this includes the ID of the entity being commented on in the response, allowing clients to identify which specific course, lesson, or assignment the comment is associated with.
        'created_at' => $this->created_at->toDateTimeString(),
        'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
