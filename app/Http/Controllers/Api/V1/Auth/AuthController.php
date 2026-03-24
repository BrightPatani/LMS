<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use Symfony\Component\HttpFoundation\JsonResponse;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService
    ) {}

    public function register(RegisterRequest $request): JsonResponse 
    {
        $result = $this->authService->register($request->validated());

        return response()->json([
            'message' => 'Registration successful',
            'user' => new UserResource($result['user']),
            'token' => $result['token'],
        ]);
    }
}
