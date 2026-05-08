<?php

namespace App\Repositories;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use App\Models\Submission;
use Illuminate\Pagination\LengthAwarePaginator;

class InstructorCourseRepository
{
    // Verify the course belongs to this instructor
    public function findOwnedCourse(int $courseId, int $instructorId): ?Course
    {
        return Course::where('id', $courseId)
                      ->where('user_id', $instructorId)
                      ->first();
    }

    // Paginated list of enrolled students with progress
    public function getEnrolledStudents(int $courseId, int $instructorId): LengthAwarePaginator
    {
        return Enrollment::where('course_id', $courseId)
                          ->with(['user:id,name,email,created_at'])
                          ->latest()
                          ->paginate(20);
    }

    // Paginated submissions across all assignments in a course
    public function getCourseSubmissions(
        int $courseId,
        int $instructorId,
        ?string $status = null
    ): LengthAwarePaginator {
        $query = Submission::whereHas(
                'assignment', fn($q) => $q->where('course_id', $courseId)
            )
            ->with([
                'student:id,name,email',
                'assignment:id,title,due_date',
                'gradedBy:id,name',
            ]);

        // Filter by status if provided — submitted | graded
        if ($status) {
            $query->where('status', $status);
        }

        return $query->latest()->paginate(20);
    }

    // Analytics data for a specific course
    public function getCourseAnalytics(int $courseId): array
    {
        $course = Course::withCount(['enrollments', 'lessons', 'assignments'])
                         ->findOrFail($courseId);

        // Completion stats — students who finished all lessons
        $totalLessons = $course->lessons_count;

        $completedStudents = 0;
        if ($totalLessons > 0) {
            $completedStudents = LessonProgress::where('course_id', $courseId)
                ->selectRaw('user_id, COUNT(*) as completed')
                ->groupBy('user_id')
                ->havingRaw('completed >= ?', [$totalLessons])
                ->count();
        }

        // Submission stats
        $totalSubmissions  = Submission::whereHas(
            'assignment', fn($q) => $q->where('course_id', $courseId)
        )->count();

        $gradedSubmissions = Submission::whereHas(
            'assignment', fn($q) => $q->where('course_id', $courseId)
        )->whereNotNull('grade')->count();

        // Average grade across all graded submissions
        $averageGrade = Submission::whereHas(
            'assignment', fn($q) => $q->where('course_id', $courseId)
        )
        ->whereNotNull('grade')
        ->avg('grade');

        // Grade distribution — how many in each bracket
        $gradeDistribution = [
            '90-100' => Submission::whereHas('assignment', fn($q) => $q->where('course_id', $courseId))
                ->whereBetween('grade', [90, 100])->count(),
            '70-89'  => Submission::whereHas('assignment', fn($q) => $q->where('course_id', $courseId))
                ->whereBetween('grade', [70, 89])->count(),
            '50-69'  => Submission::whereHas('assignment', fn($q) => $q->where('course_id', $courseId))
                ->whereBetween('grade', [50, 69])->count(),
            '0-49'   => Submission::whereHas('assignment', fn($q) => $q->where('course_id', $courseId))
                ->whereBetween('grade', [0, 49])->count(),
        ];

        // Enrollment trend — last 30 days grouped by day
        $enrollmentTrend = \App\Models\Enrollment::where('course_id', $courseId)
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn($r) => ['date' => $r->date, 'count' => $r->count]);

        return [
            'course'              => [
                'id'     => $course->id,
                'title'  => $course->title,
                'status' => $course->status,
            ],
            'enrollments'         => [
                'total'             => $course->enrollments_count,
                'completed_course'  => $completedStudents,
                'in_progress'       => $course->enrollments_count - $completedStudents,
                'completion_rate'   => $course->enrollments_count > 0
                    ? round(($completedStudents / $course->enrollments_count) * 100, 1)
                    : 0,
                'trend_last_30_days' => $enrollmentTrend,
            ],
            'lessons'             => [
                'total' => $totalLessons,
            ],
            'submissions'         => [
                'total'             => $totalSubmissions,
                'graded'            => $gradedSubmissions,
                'pending'           => $totalSubmissions - $gradedSubmissions,
                'average_grade'     => $averageGrade ? round($averageGrade, 1) : null,
                'grade_distribution' => $gradeDistribution,
            ],
        ];
    }
}