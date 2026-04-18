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

    // Combine the user data and the token into one data array
    return $this->successResponse(
        [
            'user' => new UserResource($this->authService->me()),
            'token' => $token,
            'token_type' => 'Bearer'
        ],
        'Login successful.',
        200
    );
}


    public function logout(): JsonResponse
    {
        try {
            $this->authService->logout();
            return $this->successResponse(null, 'Logout successful.');
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error_message' => 'Logout failed: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }

  public function user(): JsonResponse
{
    $user = $this->authService->me();

    if (!$user) {
        return $this->errorResponse('Unauthorized', 401);
    }

    return $this->successResponse(
        new UserResource($user),
        'User retrieved successfully.'
    );
}
}
