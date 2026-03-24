<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Assignment extends Model
{
    protected $fillable = [
        'course_id',
        'title',
        'description',
        'due_date',
    ];

    protected $casts = [
        'due_date' => 'datetime',
    ];

    public function course(): BelongsTo // this defines that an assignment belongs to a particular course.
    {
        return $this->belongsTo(Course::class);
    }

    public function submissions(): HasMany // this defines that an assignment has many submissions.
    {
        return $this->hasMany(Submission::class);
    }

    public function comments(): MorphMany // this defines that an assignment has many comments.
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
