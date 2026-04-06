<?php

namespace App\Jobs;

use App\Models\Certificate;
use App\Models\User;
use App\Models\Course;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;

class GenerateCertificate implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    public int $tries = 3; // Number of times to retry the job if it fails

    /**
     * Create a new job instance.
     */
    public function __construct(
        public readonly User $user,
        public readonly Course $course
    )
    {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $exists = Certificate::where('user_id', $this->user->id)
            ->where('course_id', $this->course->id)
            ->exists();

        if (!$exists) { // Check if certificate already exists to prevent duplicates
           return;
        }

        $path = "certificates/user_{$this->user->id}_course_{$this->course->id}.pdf";

        Certificate::create([
            'user_id' => $this->user->id,
            'course_id' => $this->course->id,
            'path' => $path,
            'issued_at' => now(),
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        // Log the failure or notify admins
        Log::error('Failed to generate certificate', [
            'user_id' => $this->user->id,
            'course_id' => $this->course->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
