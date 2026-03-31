<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Assignment\StoreAssignmentRequest;
use Illuminate\Http\Request;
use App\Http\Resources\AssignmentResource;
use App\Services\AssignmentService;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Traits\ApiResponseTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AssignmentController extends Controller
{

    use ApiResponseTrait;
    use AuthorizesRequests;
    public function __construct(
        private AssignmentService $assignmentService    
    ){}

    public function store(StoreAssignmentRequest $request): JsonResponse
    {
        $assignment = $this ->assignmentService->create($request-validated());
        return $this->successResponse(
            new AssignmentResource($assignment),
            'Assignment created successfully.',
            201
        );
    }
}
