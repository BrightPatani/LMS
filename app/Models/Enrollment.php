<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enrollment extends Model
{
    protected $fillable = [
        'user_id',
        'course_id',
        'enrolled_at',
    ];

    public function user(): BelongsTo // this defines that an enrollment belongs to a particular student.
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo // this defines that an enrollment belongs to a particular course.
    {
        return $this->belongsTo(Course::class);
    }
}
