<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role?->value ?? $this->role,
            'is_active' => $this->is_active,
            'stats' => $this->when(
                isset($this->enrollments_count),
                fn() => [
                    'enrollments' => $this->enrollments_count ?? 0,
                    'courses_teaching' => $this->courses_teaching_count ?? 0,
                    'submissions' => $this->submissions_count ?? 0,
                ]
            ),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
