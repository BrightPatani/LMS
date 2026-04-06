<?php

namespace App\Providers;

use App\Models\Course;
use App\Policies\CoursePolicy;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\EnrollmentCreated;
use App\Events\CourseCompleted;
use App\Listeners\SendEnrollmentEmailListener;
use App\Listeners\GenerateCertificateListener;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(EnrollmentCreated::class, SendEnrollmentEmailListener::class);
        Event::listen(CourseCompleted::class, GenerateCertificateListener::class);
    }

    protected $policies = [
    Course::class => CoursePolicy::class,
    ];
}
