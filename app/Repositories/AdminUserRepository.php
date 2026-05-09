<?php

namespace App\Repositories;

use App\Models\Assignment;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class AdminUserRepository
{
    public function paginate(array $filters = []): LengthAwarePaginator
    {
        $query = User::query();

        // Filter by role
        if (!empty($filters['role'])) {
            $query->where('role', $filters['role']);
        }

        // Filter by active status
        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        // Search by name or email
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                  ->orWhere('email', 'like', "%{$filters['search']}%");
            });
        }

        return $query->latest()->paginate(20);
    }

    public function findById(int $id): ?User
    {
        return User::withCount([
            'enrollments',
            'coursesTeaching',
            'submissions',
        ])->find($id);
    }

    public function update(User $user, array $data): User
    {
        $user->update($data);
        return $user->fresh();
    }

    public function deactivate(User $user): User
    {
        $user->update(['is_active' => false]);
        return $user->fresh();
    }

    public function activate(User $user): User
    {
        $user->update(['is_active' => true]);
        return $user->fresh();
    }

    public function getPlatformStats(): array
    {
        return [
            'users' => [
                'total'       => User::count(),
                'admins'      => User::where('role', 'admin')->count(),
                'instructors' => User::where('role', 'instructor')->count(),
                'students'    => User::where('role', 'student')->count(),
                'active'      => User::where('is_active', true)->count(),
                'inactive'    => User::where('is_active', false)->count(),
            ],
            'courses' => [
                'total'     => Course::count(),
                'published' => Course::where('status', 'published')->count(),
                'draft'     => Course::where('status', 'draft')->count(),
            ],
            'enrollments' => [
                'total'          => Enrollment::count(),
                'last_30_days'   => Enrollment::where(
                    'created_at', '>=', now()->subDays(30)
                )->count(),
            ],
            'content' => [
                'total_lessons'     => Lesson::count(),
                'total_assignments' => Assignment::count(),
                'total_submissions' => Submission::count(),
                'total_certificates'=> Certificate::count(),
            ],
            'activity' => [
                'new_users_today'  => User::whereDate('created_at', today())->count(),
                'new_users_week'   => User::where('created_at', '>=', now()->subWeek())->count(),
                'new_users_month'  => User::where('created_at', '>=', now()->subMonth())->count(),
            ],
        ];
    }
}