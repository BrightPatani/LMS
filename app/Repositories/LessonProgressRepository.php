<?php 

namespace App\Repositories;

use App\Models\LessonProgress;
use App\Models\Lesson;
use Illuminate\Support\Collection;

class LessonProgressRepository
{
    public function isCompleted(int $userId, int $lessonId): bool
    {
        return LessonProgress::where('user_id', $userId)
            ->where('lesson_id', $lessonId)
            ->exists();
    }

    public function markComplete(int $userId, int $lessonId, int $courseId): LessonProgress
    {
        return LessonProgress::firstOrCreate(
            [
                'user_id' => $userId,
                'lesson_id' => $lessonId,
            ],
            [
                'course_id' => $courseId,
                'completed_at' => now(),
            ]
        ); // This will create a new record if it doesn't exist, or return the existing one if it does.
    }

    public function getCompletedLessonIds(int $userId, int $courseId): Collection
    {
        return LessonProgress::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->pluck('lesson_id'); // This retrieves a collection of lesson IDs that the user has completed for a specific course.
    }

    public function getCompletionStats(int $userId, int $courseId): array
    {
        $totallessons = Lesson::where('course_id', $courseId)->count();
        $completedLessons = LessonProgress::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->count();

        return [
            'total_lessons' => $totallessons,
            'completed_lessons' => $completedLessons,
            'completion_percentage' => $totallessons > 0 ? round(($completedLessons / $totallessons) * 100, 1) : 0, // this calculates the completion percentage and rounds it to one decimal place, ensuring that if there are no lessons, it returns 0% to avoid division by zero errors.
            'is_complete' => $totallessons > 0 && $completedLessons >= $totallessons, 
        ];

    }

    public function getProgressForcourses(int $userId, array $courseIds): Collection
    {
        return LessonProgress::where('user_id', $userId)
            ->whereIn('course_id', $courseIds)
            ->selectRaw('course_id, COUNT(*) as completed_count')
            ->groupBy('course_id')
            ->get()
            ->keyBy('course_id'); // this retrieves the progress for multiple courses for a user, grouping the results by course ID and counting the number of completed lessons for each course.
    }
}