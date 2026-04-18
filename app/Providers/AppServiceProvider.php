<?php

namespace App\Providers;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Assignment;
use App\Policies\CoursePolicy;
use App\Policies\LessonPolicy;
use App\Policies\AssignmentPolicy;
use App\Events\EnrollmentCreated;
use App\Events\CourseCompleted;
use App\Listeners\SendEnrollmentEmailListener;
use App\Listeners\GenerateCertificateListener;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Policy Registration
        Gate::policy(Course::class, CoursePolicy::class);
        Gate::policy(Lesson::class, LessonPolicy::class);
        Gate::policy(Assignment::class, AssignmentPolicy::class);

        // Event Listeners
        Event::listen(EnrollmentCreated::class, SendEnrollmentEmailListener::class);
        Event::listen(CourseCompleted::class, GenerateCertificateListener::class);

        // API Rate Limiter
        RateLimiter::for('api', function (Request $request) {

            $userId = auth('api')->id(); // Get the authenticated user's ID, or null if not authenticated

            return Limit::perMinute(60)
                ->by($userId ?: $request->ip())
                ->response(function () {
                    return response()->json([
                        'success' => 'error',
                        'message' => 'Too many requests. Please slow down.',
                    ], 429);
                });
        });

        // Auth Rate Limiter
        RateLimiter::for('auth', function (Request $request) {
            $userId = auth('api')->id();
            return Limit::perMinute(10)
                ->by($userId ?: $request->ip())
                ->response(function () {
                    return response()->json([
                        'success' => 'error',
                        'message' => 'Too many login attempts. Please try again later.',
                    ], 429);
                });
        });

        // Uploads Rate Limiter
        RateLimiter::for('uploads', function (Request $request) {
            $userId = auth('api')->id();
            
            return Limit::perMinute(20)
                ->by($userId ?: $request->ip())
                ->response(function () {
                    return response()->json([
                        'success' => 'error',
                        'message' => 'Too many upload attempts. Please try again later.',
                    ], 429);
                });
        });
    }
}
