<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\AdminUserRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class AdminService
{
    public function __construct(
        private AdminUserRepository $userRepository,
        private ActivityLogService  $activityLogService
    ) {}

    public function listUsers(array $filters): LengthAwarePaginator
    {
        return $this->userRepository->paginate($filters);
    }

    public function getUser(int $id): ?User
    {
        return $this->userRepository->findById($id);
    }

    public function updateUser(User $user, array $data, int $adminId): User
    {
        $updated = $this->userRepository->update($user, $data);

        $this->activityLogService->log(
            action:      'admin.user_updated',
            description: "Admin #{$adminId} updated user #{$user->id}",
            subject:     $updated,
            properties:  $data,
        );

        return $updated;
    }

    public function changeRole(User $user, string $role, int $adminId): User
    {
        // Prevent admin from demoting themselves
        if ($user->id === $adminId) {
            throw new \RuntimeException('You cannot change your own role.');
        }

        $oldRole = $user->role->value;
        $updated = $this->userRepository->update($user, ['role' => $role]);

        $this->activityLogService->log(
            action:      'admin.role_changed',
            description: "Admin #{$adminId} changed user #{$user->id} role from {$oldRole} to {$role}",
            subject:     $updated,
            properties:  ['old_role' => $oldRole, 'new_role' => $role],
        );

        return $updated;
    }

    public function toggleActive(User $user, int $adminId): User
    {
        // Prevent admin from deactivating themselves
        if ($user->id === $adminId) {
            throw new \RuntimeException('You cannot deactivate your own account.');
        }

        $updated = $user->is_active
            ? $this->userRepository->deactivate($user)
            : $this->userRepository->activate($user);

        $this->activityLogService->log(
            action:      $updated->is_active ? 'admin.user_activated' : 'admin.user_deactivated',
            description: "Admin #{$adminId} " . ($updated->is_active ? 'activated' : 'deactivated') . " user #{$user->id}",
            subject:     $updated,
        );

        return $updated;
    }

    public function deleteUser(User $user, int $adminId): void
    {
        if ($user->id === $adminId) {
            throw new \RuntimeException('You cannot delete your own account.');
        }

        $this->activityLogService->log(
            action:      'admin.user_deleted',
            description: "Admin #{$adminId} deleted user #{$user->id} ({$user->email})",
            subject:     $user,
        );

        $user->delete();
    }

    public function getPlatformStats(): array
    {
        return $this->userRepository->getPlatformStats();
    }

}