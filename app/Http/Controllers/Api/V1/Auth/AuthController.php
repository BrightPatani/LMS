<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Traits\ApiResponseTrait;


class AuthController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private AuthService $authService
    ) {}

    public function register(RegisterRequest $request): JsonResponse 
    {
        $result = $this->authService->register($request->validated());

        return $this->successResponse(
            new UserResource($result['user']), 
            'User registered successfully.',
            201,
            ['token' => $result['token']]
        );
    }

    public function login(LoginRequest $request): JsonResponse
    {
      $token = $this->authService->login($request->validated());

      if (!$token) {
          return $this->errorResponse('Invalid credentials.', 401);
      }

      return $this->successResponse(
          new UserResource($this->authService->me()),
          'Login successful.',
          200,
          ['token' => $token]
      );

    }

    public function logout(): JsonResponse
    {
        $this->authService->logout();
        return $this->successResponse(null, 'Logged out successfully.');
    }

    public function me(): JsonResponse
    {
        return $this->successResponse(
            new UserResource($this->authService->me()),
            'User retrieved successfully.'
        );
    }
}
