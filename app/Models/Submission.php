<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Submission extends Model
{
    protected $fillable = [
        'assignment_id',
        'user_id',
        'content',
        'file_path',
        'status',
        'grade',
        'feedback',
        'graded_at',
        'graded_by',
    ];

    protected $casts = [
        'graded_at' => 'datetime',
        'grade' => 'integer',
    ];

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function gradedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    public function getStatusLabelAttribute(): string 
    {
        if ($this->grade !== null) return 'graded';
        return $this->status;
    }
}
