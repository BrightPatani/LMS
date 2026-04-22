<?php

namespace App\Services;

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
        
        $courseIds = $enrollments->pluck('course_id')->toArray();
    } // Get all enrolled course IDs 

    $progressMap = $this->progressRepository->getProgressForCourses($userId, $courseIds); //get progress for all enrolled courses 

    $enrolledCourses = $enrollments->map(function ($enrollment) use ($progressMap) {
        
    })
}