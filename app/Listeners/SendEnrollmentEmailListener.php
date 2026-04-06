<?php

namespace App\Listeners;

use App\Events\EnrollmentCreated;
use App\Jobs\SendEnrollmentEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\InteractsWithSockets;


class SendEnrollmentEmailListener implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels, Dispatchable, Queueable;
    /**
     * Create the event listener.
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     */
    public function handle(EnrollmentCreated $event): void
    {
        // Send enrollment email logic here
        // Example: Mail::to($event->user)->send(new EnrollmentMail($event->enrollmentId));
        SendEnrollmentEmail::dispatch($event->enrollmentId);
    }
}
