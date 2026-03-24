<?php

namespace App\Services;

use App\Models\Lesson;
use App\Repositories\LessonRepository;

class LessonService
{
    public function __construct(
        private LessonRepository $lessonRepository,
    ) {}

    public function getByCourse(int $courseId)
    {
        return $this->lessonRepository->getByCourseId($courseId); // this retrieves all lessons associated with a specific course ID using the LessonRepository.
    }

    public function getById(int $id): ?Lesson
    {
        return $this->lessonRepository->findById($id); // this retrieves a specific lesson by its ID using the LessonRepository.
    }

    public function createLesson(array $data): Lesson
    {
        return $this->lessonRepository->create($data); // this creates a new lesson with the provided data using the LessonRepository.
    }

    public function updateLesson(Lesson $lesson, array $data): Lesson
    {
        return $this->lessonRepository->update($lesson, $data); // this updates an existing lesson with the new data using the LessonRepository.
    }

    public function deleteLesson(Lesson $lesson): void
    {
        $this->lessonRepository->delete($lesson); // this deletes the specified lesson using the LessonRepository.
    }
}