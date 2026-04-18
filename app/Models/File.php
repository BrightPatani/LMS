<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    protected $fillable = [
        'lesson_id',
        'original_name',
        'path',
        'file_link',
        'mime_type',
        'size',
        'created_at',
        'updated_at',
    ];

    /**
     * Get the URL for the file.
     * If file_link exists (external), use it. 
     * Otherwise, generate a URL from the internal path.
     */
    protected function url(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->file_link) {
                    return $this->file_link;
                }
                return $this->path ? Storage::url($this->path) : null;
            },
        );
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
}
