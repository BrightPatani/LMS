<?php

namespace App\Services;

use App\Models\Comment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;


class CommentService
{
    private array $typeMap = [
        'course' => \App\Models\Course::class,
        'lesson' => \App\Models\Lesson::class,
        'assignment' => \App\Models\Assignment::class,
    ];

    public function create(array $data, int $userId): Comment
    {
       $modelClass = $this->typeMap[$data['commentable_type']];

        $model = $modelClass::findOrFail($data['commentable_id']);

        return Comment::create([
            'user_id' => $userId,
            'commentable_id' => $model->id,
            'commentable_type' => $modelClass,
            'body' => $data['body'],
        ]);
    }

    public function getFor(string $type, int $id): LengthAwarePaginator
    {
        $modelClass = $this->typeMap[$type] ?? throw new \InvalidArgumentException("Invalid commentable type.");

        return Comment::where('commentable_id', $id)
            ->where('commentable_type', $modelClass)
            ->with('author')
            ->latest()
            ->paginate(15);
    }
}