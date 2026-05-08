<?php

namespace App\Http\Controllers\Api\V1\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Instructor\GradeSubmissionRequest;
use App\Http\Resources\SubmissionResource;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\Submission;
use App\Repositories\InstructorCourseRepository;
use App\Repositories\LessonProgressRepository;
use App\Services\CoursePublishService;
use App\Services\InstructorDashboardService;
use App\Services\InstructorSubmissionService;
use App\Traits\ApiResponseTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\JsonResponse;

class InstructorController extends Controller
{

    use ApiResponseTrait, AuthorizesRequests;

    public function __construct(
        private InstructorDashboardService $dashboardService,
        private InstructorCourseRepository $courseRepository,
        private InstructorSubmissionService $submissionService,
        private CoursePublishService $PublishService,
    ) {}

    // instructor dashboard (get)
    public function dashboard(): JsonResponse
    {
        $data = $this->dashboardService->getDashboard(Auth::id());
        return $this->successResponse(
            $data,
            'Instructor dashboard loaded succesfully.'
        );
    }

    public function courseStudents(Course $course): JsonResponse
    {
        if ($course->user_id !== Auth::id()) {
            return $this->errorResponse(
                'You are not the instructor of this course.',
                403
            );
        }
        $enrollments = $this->courseRepository->getEnrolledStudents($course->id, Auth::id());
        $data = $enrollments->through(function ($enrollment) use ($course) {
            $progressRepo = app(LessonProgressRepository::class);
            $stats = $progressRepo->getCompletionStats($enrollment->user_id, $course->id);

            return [
                'student_id' => $enrollment->user->id,
                'name' => $enrollment->user->name,
                'email' => $enrollment->user->email,
                'enrolled_at' => $enrollment->enrolled_at,
                'progress' => [
                    'completed_lessons' => $stats['completed_lessons'],
                    'total_lessons' => $stats['total_lessons'],
                    'percentage' => $stats['percentage'],
                    'is_complete' => $stats['is_complete'],
                ],
            ];
        });
        return $this->paginatedResponse(
            $this->wrapPaginatedData($enrollments, $data->items()),
        );
    }

     // Get submissions 
    public function courseSubmissions(Request $request, Course $course): JsonResponse
        {
            if ($course->user_id !== Auth::id()) {
                return $this->errorResponse(
                    'You are not the instructor of this course.',
                    403
                );
            }

            $status = $request->query('status'); // Optional filter
            $submissions = $this->courseRepository->getCourseSubmissions($course->id, Auth::id(), $status);
            return $this->paginatedResponse(SubmissionResource::collection($submissions));
        }

        // grading
    public function gradeSubmission(
        GradeSubmissionRequest $request,
        Submission $submission
    ): JsonResponse {
        $result = $this->submissionService->grade(
            $submission->id,
            Auth::id(),
            $request->validated()
        );

        if (!$result['success']) {
            return $this->errorResponse(
                $result['message'], $result['code']
            );
        }
        return $this->successResponse(
            new SubmissionResource($result['submission']),
            'Submission graded successfully.'
        );
    }

    // Get analytics for a course
    public function courseAnalytics(Course $course): JsonResponse
    {
        if ($course->user_id !== Auth::id()) {
            return $this->errorResponse(
                'You are not the instructor of this course.',
                403
            );
        }

        $analytics = $this->courseRepository->getCourseAnalytics($course->id);
        return $this->successResponse(
            $analytics,
            'Course analytics loaded successfully.'
        );
    }

    //toggle publish course 
    public function togglePublish(Course $course): JsonResponse
    {
        $result = $this->PublishService->toggle($course, Auth::id());
        if (!$result['success']) {
            return $this->errorResponse(
                $result['message'], $result['code']
            );
        }
        return $this->successResponse([
            'course_id' => $result['course_id'],
            'new_status' => $result['new_status'],
        ], $result['message']);
    } 

    // get assignments
    public function courseAssignments(Course $course): JsonResponse
    {
        if ($course->user_id !== Auth::id()) {
            return $this->errorResponse('You are not the instructor of this course.', 403);
        }
        $assignments = Assignment::where('course_id', $course->id) 
            ->withCount(['submissions'])
            ->with([
                'submissions' => fn($q) => $q->whereNull('grade'),
            ])
            ->latest()
            ->paginate(20);

            $data = $assignments->through(fn($a) => [
                'assignment_id' => $a->id,
                'title' => $a->title,
                'due_date' => $a->due_date?->toDateTimeString(),
                'is_overdue' => $a->due_date ?->isPast() ?? false,
                'total_submissions' => $a->submissions_count,
                'pending_grading' => $a->submissions->count(),
            ]);

            return $this->paginatedResponse(
                $this->wrapPaginatedData($assignments, $data->items())
            );
    }

    // view submissions for a particular assignment 
    public function assignmentSubmissions (Course $course, int $assignmentId): JsonResponse
    {
        if ($course->user_id !== Auth::id()) {
            return $this->errorResponse(
                'You are not the instructor of this course.', 403);
        }

        $submissions = Submission::where('assignment_id', $assignmentId)
            ->whereHas('assignment', fn($q) => $q->where('course_id', $course->id))
            ->with([
                'student:id,name,email',
                'assignment:id,title',
                'gradedBy:id,name',
            ])
            ->latest()
            ->paginate(20);
        return $this->paginatedResponse(SubmissionResource::collection($submissions));
    }

    private function wrapPaginatedData($paginator, array $items): JsonResponse
    {
        return response()->json([
            'data' => $items,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
            'links' => [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],
        ]);
    }
}
