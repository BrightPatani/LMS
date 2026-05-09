<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ChangeUserRoleRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\AdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    use ApiResponseTrait, AuthorizesRequests;

    public function __construct(
        private AdminService $adminService
    ) {}

    // ── GET /admin/users ──────────────────────
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['role', 'is_active', 'search']);

        // Cast is_active to boolean if provided
        if (isset($filters['is_active'])) {
            $filters['is_active'] = filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN);
        }

        $users = $this->adminService->listUsers($filters);

        return $this->paginatedResponse(UserResource::collection($users));
    }

    // ── GET /admin/users/{user} ───────────────
    public function show(User $user): JsonResponse
    {
        $user = $this->adminService->getUser($user->id);
        return $this->successResponse(new UserResource($user));
    }

    // ── PUT /admin/users/{user} ───────────────
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $updated = $this->adminService->updateUser(
            $user,
            $request->validated(),
            Auth::id()
        );

        return $this->successResponse(new UserResource($updated), 'User updated');
    }

    // ── PATCH /admin/users/{user}/role ────────
    public function changeRole(ChangeUserRoleRequest $request, User $user): JsonResponse
    {
        try {
            $updated = $this->adminService->changeRole(
                $user,
                $request->validated()['role'],
                Auth::id()
            );
        } catch (\RuntimeException $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }

        return $this->successResponse(new UserResource($updated), 'User role updated');
    }

    // ── PATCH /admin/users/{user}/toggle-active
    public function toggleActive(User $user): JsonResponse
    {
        try {
            $updated = $this->adminService->toggleActive($user, Auth::id());
        } catch (\RuntimeException $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }

        $message = $updated->is_active ? 'User activated' : 'User deactivated';

        return $this->successResponse(new UserResource($updated), $message);
    }

    // ── DELETE /admin/users/{user} ────────────
    public function destroy(User $user): JsonResponse
    {
        try {
            $this->adminService->deleteUser($user, Auth::id());
        } catch (\RuntimeException $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }

        return $this->successResponse(null, 'User deleted successfully');
    }
}