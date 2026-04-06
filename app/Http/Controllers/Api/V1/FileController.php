<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Services\FileService;
use App\Http\Requests\File\UploadThumbnailRequest;
use App\Http\Requests\File\UploadLessonFileRequest;
use App\Models\Course;
use Nette\Utils\Json;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Models\Lesson;

class FileController extends Controller
{
    use ApiResponseTrait;
    use AuthorizesRequests;

    public function __construct(
        private FileService $fileService
    ){}

    public function uploadThumbnail(UploadThumbnailRequest $request, Course $course) : JsonResponse
    {
        $this->authorize('update', $course);
        $path = $this->fileService->uploadCourseThumbnail(
            $request->file('thumbnail'),
            $course->id
        );
        return $this->successResponse(['path' => $path], 'Thumbnail uploaded successfully.');
    }

    public function uploadLessonFile(UploadLessonFileRequest $request, Lesson $lesson): JsonResponse
    {
        $this->authorize('update', $lesson);
        $file = $this->fileService->uploadLessonFile(
            $request->file('file'),
            $lesson->id
        );
        return $this->successResponse(['file' => $file], 'File uploaded successfully.', 201);
    }

    public function destroy(int $id): JsonResponse
    {
        $file = \App\Models\File::findOrFail($id);
        $this->authorize('delete', $file->lesson);
        $this->fileService->delete($file->path);
        $file->delete();
        return $this->successResponse(["message" => 'File deleted successfully.']);
    }
}
