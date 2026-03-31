<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Enrollment\EnrollCourseRequest;
use Illuminate\Http\Request;
use App\Http\Resources\EnrollmentResource;
use App\Models\Enrollment;  
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Auth;
use Nette\Utils\Json;
use App\Services\EnrollmentService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class EnrollmentController extends Controller
{
  use ApiResponseTrait, AuthorizesRequests;

  private EnrollmentService $enrollmentService;

  public function __construct(EnrollmentService $enrollmentService)
  {
    $this->enrollmentService = $enrollmentService;
  }
  
    public function enroll(EnrollCourseRequest $request): JsonResponse
    {
        $enrollment = $this->enrollmentService->enrollStudent(Auth::id(), $request->course_id);
        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to enroll in course.',
            ], 409);
        }

        return $this->successResponse(
            new EnrollmentResource($enrollment),
            'Enrolled in course successfully.',
            201
        );
    }

    public function myCourses(): JsonResponse
    {
        $courses = $this->enrollmentService->getUserCourses(Auth::id());
        return $this->successResponse(
            EnrollmentResource::collection($courses),
            'User courses retrieved successfully.'
        );
    } // this method retrieves the courses that the authenticated user is enrolled in by calling the getUserCourses method of the EnrollmentService, passing the user's ID as a parameter. It then returns a JSON response with a collection of EnrollmentResource instances representing the user's courses, along with a success message.

    public function show(Enrollment $enrollment): JsonResponse
    {
        $this->authorize('view', $enrollment);
        return $this->successResponse(
            new EnrollmentResource($enrollment),
            'Enrollment details retrieved successfully.'
        );
    } // this method retrieves the details of a specific enrollment by accepting an Enrollment model instance as a parameter. It first checks if the authenticated user has permission to view the enrollment using the authorize method. If authorized, it returns a JSON response with an EnrollmentResource instance representing the enrollment details, along with a success message.

    public function destroy(Enrollment $enrollment): JsonResponse
    {
        $this->authorize('delete', $enrollment);
        $enrollment->delete();
        return $this->successResponse(
            null,
            'Enrollment deleted successfully.'
        );
    } // this method deletes a specific enrollment by accepting an Enrollment model instance as a parameter. It first checks if the authenticated user has permission to delete the enrollment using the authorize method. If authorized, it deletes the enrollment and returns a JSON response with a success message indicating that the enrollment was deleted successfully.

}