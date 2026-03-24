<?php

namespace App\Services;

use App\Repositories\UserRepository;

class AuthService {
    public function __construct(private UserRepository $userRepo) {}

    public function register(array $data): array {
        $user = $this->userRepo->create([...$data, 'password' => bcrypt($data['password'])]); // Hash the password before saving
        $token = auth()->guard()->login($user);
        return ['user' => $user, 'token' => $token];
    }
}