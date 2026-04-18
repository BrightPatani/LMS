<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CertificateResource extends JsonResource
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
            'course' => new CourseResource($this->whenloaded('course')), // this loads the associated course of the certificate and returns it as a CourseResource, providing details about the course for which the certificate was issued, but only if the 'course' relationship has been loaded.
            'issued_at' => $this->issued_at->toDateTimeString(), // this includes the issued_at timestamp in the response, indicating when the certificate was issued, and formats it as a date-time string for better readability.
            'file_path' => $this->file_path, // this includes the file path of the certificate in the response, allowing clients to access the location of the certificate file if it exists.
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
