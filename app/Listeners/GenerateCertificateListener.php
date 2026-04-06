<?php

namespace App\Listeners;

use App\Events\CourseCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Jobs\GenerateCertificate;

class GenerateCertificateListener implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CourseCompleted $event): void
    {
        GenerateCertificate::dispatch($event->user, $event->course); // Dispatch the job to generate the certificate
    }
}
