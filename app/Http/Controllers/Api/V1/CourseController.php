<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Services\CourseService;
use App\Http\Resources\CourseResource;
use App\Http\Requests\Course\StoreCourseRequest;
use App\Http\Requests\Course\UpdateCourseRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Traits\ApiResponseTrait;

class CourseController extends Controller
{
    use AuthorizesRequests; 

    use ApiResponseTrait;

    public function __construct(
        private CourseService $courseService,
    ) {}

    public function index(): JsonResponse
        {
            $courses = $this->courseService->getAllCourses();
            return $this->successResponse(
                CourseResource::collection($courses),
                'Courses retrieved successfully.'
            );
        }

    public function store(StoreCourseRequest $request): JsonResponse
        {
            $this->authorize('create', Course::class); // this checks if the authenticated user has permission to create a course using the CoursePolicy.
            $course = $this->courseService->createCourse($request->validated(), Auth::id()); // this creates a new course using the CourseService with the validated request data and the authenticated user's ID as the instructor.
            return $this->successResponse(
                new CourseResource($course),
                'Course created successfully.',
                201
            );
        }
    public function show(Course $course): JsonResponse
        {
            $this->authorize('view', $course);
            $course = $this->courseService->getCourseById($course->id);
            return $this->successResponse(
                new CourseResource($course),
                'Course retrieved successfully.'
            );
        }
    public function update(UpdateCourseRequest $request, Course $course): JsonResponse
        {
            $this->authorize('update', $course); // this checks if the authenticated user has permission to update the course using the CoursePolicy.
            $updatedCourse = $this->courseService->updateCourse($course->id, $request->validated()); // this updates the course using the CourseService with the validated request data.
            return $this->successResponse(
                new CourseResource($updatedCourse),
                'Course updated successfully.',
                200
            );
        }
    public function destroy(Course $course): JsonResponse
        {
            $this->authorize('delete', $course); // this checks if the authenticated user has permission to delete the course using the CoursePolicy.
            $this->courseService->deleteCourse($course->id); // this deletes the course using the CourseService.
            return $this->successResponse(
                null,
                'Course deleted successfully.'
            );
        }
}
