<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Course;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Http\Requests\Lesson\StoreLessonRequest;
use App\Http\Requests\Lesson\UpdateLessonRequest;
use App\Services\LessonService;
use App\Http\Resources\LessonResource;
use App\Traits\ApiResponseTrait;

class LessonController extends Controller
{
    use AuthorizesRequests; // 
    
    use ApiResponseTrait;

    public function __construct(
        private LessonService $lessonService
    ) {}

    public function index(Course $course): JsonResponse
    {
        $this->authorize('view', $course);
        $lessons = $this->lessonService->getByCourse($course->id);
        return $this->successResponse(
            LessonResource::collection($lessons),
            'Lessons retrieved successfully.'
        );
    }

    public function store(StoreLessonRequest $request): JsonResponse
    {
        $this->authorize('create', Lesson::class);
        $lesson = $this->lessonService->createLesson($request->validated());
        return $this->successResponse(
            new LessonResource($lesson),
            'Lesson created successfully.',
            201
        );
    }

    public function show(Lesson $lesson): JsonResponse
    {
        $this->authorize('view', $lesson);
        $lesson = $this->lessonService->getById($lesson->id);
        return $this->successResponse(
            new LessonResource($lesson),
            'Lesson retrieved successfully.'
        );
    }

    public function update(UpdateLessonRequest $request, Lesson $lesson): JsonResponse
    {
        $this->authorize('update', $lesson);
        $updatedLesson = $this->lessonService->updateLesson($lesson, $request->validated());
        return $this->successResponse(
            new LessonResource($updatedLesson),
            'Lesson updated successfully.'
        );
    }

    public function destroy(Lesson $lesson): JsonResponse
    {
        $this->authorize('delete', $lesson);
        $this->lessonService->deleteLesson($lesson);
        return $this->successResponse(
            null,
            'Lesson deleted successfully.'
        );
    }
}
