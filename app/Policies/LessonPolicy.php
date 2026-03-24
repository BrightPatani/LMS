<?php

namespace App\Policies;

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LessonPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    // public function viewAny(User $user, Lesson $lesson): bool
    // {
    //     return true;
    // }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Lesson $lesson): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role->value === 'instructor';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Lesson $lesson): bool
    {
        return $user->id === $lesson->course->user_id; // this checks if the authenticated user is the instructor who created the course that the lesson belongs to, allowing only that instructor to update the lesson.
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Lesson $lesson): bool
    {
        return $user->id === $lesson->course->user_id; // this checks if the authenticated user is the instructor who created the course that the lesson belongs to, allowing only that instructor to delete the lesson.
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Lesson $lesson): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Lesson $lesson): bool
    {
        return false;
    }
}
