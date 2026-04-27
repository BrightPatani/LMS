<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\LessonProgress;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Submission;
use App\Repositories\LessonProgressRepository;


class StudentDashboardService
{
    public function __construct(
        private LessonProgressRepository $progressRepository
    ){}

    public function getDashboard(int $userId): array
    {
            $enrollments = Enrollment::where('user_id', $userId)
                            ->with(['course.instructor', 'course' => fn($q) => $q->withCount('lessons')])
                            ->latest()
                            ->get();
            
        $courseIds = $enrollments->pluck('course_id')->toArray(); // this extracts the course IDs from the user's enrollments, which will be used to fetch progress and other related data for those courses.


        // get progress for all enrolled course
        $progressMap = $this->progressRepository->getProgressForCourses($userId, $courseIds); 

        // build enrolled courses with progress percentage and completion status
        $enrolledCourses = $enrollments->map(function ($enrollment) use ($progressMap) {
            $course = $enrollment->course;
            $totalLessons = $course->lessons_count ?? 0;
            $completedCount = $progressMap[$course->id]?->completed_count ?? 0;
            $percentage = $totalLessons > 0 ? round(($completedCount / $totalLessons) * 100, 1) : 0;
            return [
                'course_id' => $course->id,
                'title' => $course->title,
                'instructor' => $course->instructor?->name,
                'thumbnail' => $course->thumbnail,
                'total_lessons' => $totalLessons,
                'completed_lessons' => $completedCount,
                'progress_percentage' => $percentage,
                'is_complete' => $totalLessons > 0 && $completedCount >= $totalLessons,
                'enrolled_at' => $enrollment->enrolled_at,
            ];
        }); 

        // get pending assignments for enrolled courses
        $pendingAssignments = Assignment::whereIn('course_id', $courseIds)
                ->whereDoesntHave('submissions', fn($q) => $q->where('user_id', $userId))
                ->with('course:id,title')
                ->orderBy('due_date')
                ->get()
                ->map(fn($a) => [
                    'assignment_id' => $a->id,
                    'title' => $a->title,
                    'course' => $a->course->title,
                    'due_date' => $a->due_date?->toDateTimeString(),
                    'is_overdue' => $a->due_date && $a->due_date->isPast(),
                ]);

        // get recent certificates earned
        $recentCertificates = Certificate::where('user_id', $userId)
                ->with('course:id,title')
                ->latest('issued_at')
                ->take(5)
                ->get()
                ->map(fn($c) => [
                    'certificate_id' => $c->id,
                    'course' => $c->course->title,
                    'issued_at' => $c->issued_at->toDateTimeString(),
                    'file_path' => $c->file_path,
                ]);

        // build summary stats
        $summary = [
            'total_enrolled' => $enrollments->count(),
            'completed_courses' => $enrolledCourses->where('is_complete', true)->count(),
            'in_progress' => $enrolledCourses->where('is_complete', false)
                                            ->where('completed_lessons', '>',0)->count(),
            'not_started' => $enrolledCourses->where('completed_lessons', 0)->count(),
            'pending_assignments' => $pendingAssignments->count(),
            'overdue_assignments' => $pendingAssignments->where('is_overdue', true)->count(),
            'certificate_earned' => $recentCertificates->count(),
        ];

        return [
            'summary' => $summary,
            'enrolled_courses' => $enrolledCourses,
            'pending_assignments' => $pendingAssignments,
            'recent_certificates' => $recentCertificates,
        ];
    }   
}