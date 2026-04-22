<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Course extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'thumbnail',
        'status',
    ];

        // this block is for the instructor who created the course.
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

        // this block is for the students who enrolled in the course.
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'enrollments')->withTimestamps();
    }

    public function lessons(): HasMany // this defines that a course has many lessons.
    {
        return $this->hasMany(Lesson::class);
    }

    public function assignments(): HasMany // this defines that a course has many assignments.
    {
        return $this->hasMany(Assignment::class);
    }

    public function enrollments(): HasMany // this defines that a course has many enrollments.
    {
        return $this->hasMany(Enrollment::class);
    }

    public function comments(): MorphMany // this defines that a course has many comments.
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function lessonProgress(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }
}
