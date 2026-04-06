<?php

namespace App\Services;

use App\Models\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileService
{
    private array $allowedMimeTypes = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'video/mp4',   
        'application/pdf',
        'application/zip',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ]; // this array defines the allowed MIME types for file uploads, ensuring that only specific types of files can be uploaded to the system.


    public function uploadLessonFile(UploadedFile $file, int $lessonId): File
    {
        $path = $this->store($file, 'lessons');

        return File::create([
            'lesson_id' => $lessonId,
            'original_name' => $file->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]); // this creates a new File record in the database with the associated lesson ID and file details.
    }

    public function uploadCourseThumbnail(UploadedFile $file, int $courseId): string
    {
        $course = \App\Models\Course::find($courseId);
        if ($course->thumnail) {
            Storage::disk('public')->delete($course->thumbnail); // Delete old thumbnail if it exists
        }
        $path = $this->store($file, 'courses');
        $course->update(['thumbnail' => $path]);
        return $path;
    }

    public function uploadSubmissionfile(UploadedFile $file, int $submissionId): string
    {
        $path = $this->store($file, 'submissions');
        \App\Models\Submission::find($submissionId)->update(['file_path' => $path]);
        return $path;
    }

    public function delete(string $path): void
    {
        Storage::disk('public')->delete($path); // this deletes the file at the specified path from the public storage disk.
    }

    private function store(UploadedFile $file, string $folder): string
    {
        return $file->store($folder, 'public');
    }
}