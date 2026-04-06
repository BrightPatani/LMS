<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Enrollment;
use App\Mail\EnrollmentConfirmation;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendEnrollmentEmail implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels, Dispatchable;

    public int $tries = 3; // Number of times to retry the job if it fails

    public int $backoff = 60; // Time (in seconds) to wait before retrying the job

    /**
     * Create a new job instance.
     */
    public function __construct(
        public readonly Enrollment $enrollment
    ){}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user = $this->enrollment->user;
        $course = $this->enrollment->course;

        Mail::to($user->email)->send(
            new EnrollmentConfirmation($user, $course)
        ); // Send the enrollment confirmation email
    }

    public function failed (\Throwable $exception): void 
    {
        Log::error('SendEnrollmentEmail job failed',[
            'enrollment_id' => $this->enrollment->id,
            'error' => $exception->getMessage(),
        ]); // Log the failure for debugging and monitoring purposes
    }
}
