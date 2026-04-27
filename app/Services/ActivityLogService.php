<?php

namespace App\Services;

use App\Models\Activitylog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLogService
{
    const ACTION_LESSON_COMPLETED = 'lesson.completed';
    const ACTION_PROFILE_UPDATED = 'profile.updated';
    const ACTION_PASSWORD_UPDATED = 'password.updated';
    const ACTION_COURSE_COMPLETED = 'course.completed';

    public function log(
        string $action,
        ?string $description = null,
        array $properties = [],
        ?Model $subject = null
    ): void
    {
        Activitylog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'description' => $description,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject?->id,
            'properties' => $properties,
            'ip_address' => request()->ip(),
        ]);
    }
}

