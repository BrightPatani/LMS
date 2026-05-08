<?php 

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Course;
use App\Models\Assignment;
use App\Models\Submission;
use App\Models\LessonProgress;

class InstructorDashboardService 
{
    public function getDashboard(int $instructorId): array 
    {
        $courses = Course::where('user_id', $instructorId)
            ->withCount([
                'enrollments',
                'assignments',
                'lessons',
            ])
            ->latest()
            ->get();

        $courseIds = $courses->pluck('id')->toArray();

        $totalStudents = Enrollment::whereIn('course_id', $courseIds)
            ->distinct('user_id')
            ->count('user_id');

        $pendingSubmissions = Submission::whereHas('assignment', fn($q) => $q->whereIn('course_id', $courseIds)
            )->where('status', 'submitted')
            ->whereNull('grade')
            ->with([
                'assignment:id,title,course_id',
                'assignment.course:id,title',
                'student:id,name,email',
            ])
            ->latest()
            ->take(10)
            ->get()
            ->map(fn($s) => [
                'submission_id' => $s->id,
                'student' => $s->student->name,
                'assignment' => $s->assignment->title,
                'course' => $s->assignment->course->title,
                'submitted_at' => $s->created_at->toDateTimeString(),
                'days_waiting' => $s->created_at->diffInDays(now()),
            ]);

        $recentEnrollments = Enrollment::whereIn('course_id', $courseIds)
            ->with(['user:id,name,email', 'course:id,title'])
            ->latest()
            ->take(10)
            ->get()
            ->map(fn($e) => [
                'student' => $e->user->name,
                'email' => $e->user->email,
                'course' => $e->course->title,
                'enrolled_at' => $e->enrolled_at,
            ]);
        
        $courseSummary = $courses->map(fn($c) => [
                'course_id' => $c->id,
                'title' => $c->title,
                'status' => $c->status,
                'total_students' => $c->enrollments_count,
                'total_lessons' => $c->lessons_count,
                'total_assignments' => $c->assignments_count,
                'pending_submissions' => Submission::whereHas(
                    'assignment', fn($q) => $q->where('course_id', $c->id)
                )
                ->where('status', 'submitted')
                ->whereNull('grade')
                ->count()
            ]);

        $summary = [
            'total_courses' => $courses->count(),
            'published_courses' => $courses->where('status', 'published')->count(),
            'draft_courses' => $courses->where('status', 'draft')->count(),
            'total_students' => $totalStudents,
            'pending_submissions' => $pendingSubmissions->count(),
            'total_submissions' => Submission::whereHas(
                'assignment', fn($q) => $q->whereIn('course_id', $courseIds)
            )->count(),
        ];
        
        return [
            'summary' => $summary,
            'course_summary' => $courseSummary,
            'pending_submissions' => $pendingSubmissions,
            'recent_enrollments' => $recentEnrollments,
        ];
    }
}