<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ProfileService
{
    public function __construct(
        private ActivityLogService $activityLogService
    ){}

    public function updateProfile(User $user, array $data): User 
    {
        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        $this->activityLogService->log(
            action: 'profile.updated',
            description: "User #{$user->id} updated their profile",
            subject: $user,
        );

        return $user->fresh();
    }

    public function updatePassword(User $user, array $data): array
    {
        if (!Hash::check($data['current_password'], $user->password)) {
            return [
                'success' => false,
                'message' => 'current password is incorrect.',
            ];
        }

        $user->update([
            'password' => bcrypt($data['password']),
        ]);

        $this->activityLogService->log(
            action: 'password.changed',
            description: "user #{$user->id} changed their password",
            subject: $user,
        );

        return [
            'success' => true, 
            'message' => 'Password updated successfully.',  
        ];
    }    
}