<?php

namespace App\Repositories;

use App\Models\Lesson;
use App\Http\Resources\LessonResource;

class LessonRepository
{
    public function getByCourseId(int $courseId)
    {
        return lesson::where('course_id', $courseId)
            ->orderBy('order')
            ->get();
    }

    public function findById(int $id): ?Lesson
    {
        return Lesson::with(['course', 'files'])->find($id); // this retrieves a lesson by its ID, including its associated course and files, using Eloquent's eager loading.
    }

    public function create(array $data): Lesson
    {
        return Lesson::create($data); // this creates a new lesson with the provided data using Eloquent's create method.
    }

    public function update(Lesson $lesson, array $data): Lesson
    {
        $lesson->update($data); // this updates the specified lesson with the new data using Eloquent's update method.
        return $lesson->fresh(); // this returns the updated lesson instance.
    }

    public function delete(Lesson $lesson): void
    {
        $lesson->delete(); 
    }
}