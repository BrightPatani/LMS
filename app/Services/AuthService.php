<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthService {
    public function __construct(
        private UserRepository $userRepository
    ){}

    public function register(array $data): array 
    {
        $user = $this->userRepository->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'role' => $data['role'] ?? 'student',
        ]);

        // For JWT, login() returns the token string directly
        $token = Auth::guard('api')->login($user);
        
        return ['user' => $user, 'token' => $token];
    }
    
    public function login(array $credentials): ?string
    {
        // attempt() returns the token string on success, or false on failure
        $token = Auth::guard('api')->attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ]);

        return $token ?: null; // return the token if authentication is successful, or null if it fails
    }
    
    public function logout(): void
    {
        // Invalidates the current JWT token
        Auth::guard('api')->logout(); // this method invalidates the current JWT token, effectively logging the user out by preventing further use of that token for authentication.
    }

    public function me(): ?User
    {
        // Retrieves the user based on the JWT in the request header
        return auth('api')->user();
    }
}
