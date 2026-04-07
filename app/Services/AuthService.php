<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;

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

            $token = Auth::login($user);
            return ['user' => $user, 'token' => $token];
        } // This method creates a new user with the provided data, hashes the password, and assigns a role (defaulting to 'student' if not provided). After creating the user, it generates a JWT token for the newly registered user and returns both the user and the token.
    
    public function login(array $credentials): ?string
        {
            return Auth::attempt([
                'email' => $credentials['email'],
                'password' => $credentials['password'],
            ]) ? Auth::getToken()->get() : null;
        } // This method attempts to authenticate the user with the provided email and password. If authentication is successful, it returns the generated JWT token; otherwise, it returns null.
    
    public function logout(): void
        {
            Auth::logout();
        }

    public function me(): \App\Models\User
        {
            return Auth::user();
        } // This method retrieves the currently authenticated user using the Auth facade and returns it.
}