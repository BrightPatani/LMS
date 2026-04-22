<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Lesson extends Model
{
    protected $fillable = [
        'course_id',
        'title',
        'content',
        'order',
    ];

    public function course(): BelongsTo // this defines that a lesson belongs to a particular course.
    {
        return $this->belongsTo(Course::class);
    }

    public function files(): HasMany // this defines that a lesson has many files.
    {
        return $this->hasMany(File::class);
    }

    public function comments(): MorphMany 
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function progress(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function isCompletedByUser(User $user): bool
    {
        return $this->progress()->where('user_id', $user->id)->exists();
    }
}
