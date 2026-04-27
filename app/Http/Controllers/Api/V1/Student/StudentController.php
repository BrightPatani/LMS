<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdatePasswordRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Resources\LessonProgressResource;
use App\Http\Resources\SubmissionResource;
use App\Http\Resources\UserResource;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Repositories\SubmissionRepository;
use App\Services\LessonProgressService;
use App\Services\ProfileService;
use App\Services\StudentDashboardService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Traits\ApiResponseTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Nette\Utils\Json;

class StudentController extends Controller
{
    use ApiResponseTrait, AuthorizesRequests;

    public function __construct(
        private LessonProgressService $progressService,
        private StudentDashboardService $dashboardService,
        private ProfileService $profileService,
        private SubmissionRepository $submissionRepository,
    ){}

    /**
     * Get Dashboard
     */
    public function dashboard(): JsonResponse
    {
        $data = $this->dashboardService->getDashboard(Auth::id());
        return $this->successResponse(
            $data,
            'Dashboard Loaded Successfully.'
        );
    } // this method will call the getDashboard method of the StudentDashboardService

    

    /**
     * post lesson comepleted
     */
    public function completeLesson(Lesson $lesson): JsonResponse
    {
            $result = $this->progressService->markComplete(Auth::id(), $lesson->id);
            if (!$result['success']) {
                return $this->errorResponse(
                    $result['message'],
                    403
                );
            }
            return $this->successResponse([
                'progress' => new LessonProgressResource($result['progress']),
                'stats' => $result['stats'],
            ], $result['stats']['is_complete']
                ? 'Lesson completed. Course finished - certificate is being generated!' : 'lesson marked as complete.'
        );
    }

        /**
        * get course progress
        */
    public function courseProgress(int $courseId): JsonResponse
    {
        $isEnrolled = Enrollment::where('user_id', Auth::id())
            ->where('course_id', $courseId)
            ->exists();
        if (!$isEnrolled) {
            return $this->errorResponse(
                'You must be enrolled in this course to view progress.',
                403
            );
        }

        $progress = $this->progressService->getCourseProgress(Auth::id(), $courseId);
        return $this->successResponse(
            $progress,
            'Course progress Loaded successfully.'
        );
    }

        /**
        * Get Submission
        */

        public function showSubmission(int $id): JsonResponse
        {
            $submission = $this->submissionRepository->findForStudent($id, Auth::id());
            if (!$submission) {
                return $this->errorResponse(
                    'Submission not found.',
                    404
                );
            }
            return $this->successResponse(
               new SubmissionResource($submission),
                'Submission retrieved successfully.'
            );
        }

        /**
        * update profile
        */

        public function updateProfile(UpdateProfileRequest $request): JsonResponse
        {
            $user = $this->profileService->updateProfile(Auth::user(), $request->validated());
            return $this->successResponse(new UserResource($user), 'Profile updated successfully.');
        }

        /**
        * update password
        */
        public function updatePassword(UpdatePasswordRequest $request): JsonResponse
        {
            $result = $this->profileService->updatePassword(Auth::user(), $request->validated());
            if (!$result['success']) {
                return $this->errorResponse(
                    $result['message'],
                    422
                );
            }
            return $this->successResponse(null, 'Password updated successfully.');
        }
}