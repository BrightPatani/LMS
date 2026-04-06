<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Assignment\StoreAssignmentRequest;
use App\Http\Requests\Assignment\SubmitAssignmentRequest;
use Illuminate\Http\Request;
use App\Http\Resources\SubmissionResource;
use App\Http\Resources\AssignmentResource;
use Illuminate\Http\JsonResponse;
use App\Models\Assignment;
use App\Services\AssignmentService;
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
        $assignment = $this ->assignmentService->create($request->validated());
        return $this->successResponse(
            new AssignmentResource($assignment),
            'Assignment created successfully.',
            201
        );
    }

    public function submit(SubmitAssignmentRequest $request, Assignment $assignment): JsonResponse
    {
        $result = $this->assignmentService->submit(
            $assignment->id,
            $request->user()->id,
            $request->input('content'), // this retrieves the content of the assignment submission from the request, allowing students to submit their work as text content when they submit an assignment. The content is optional, but if provided, it will be processed and stored accordingly by the service.
            $request->file('file') // this retrieves the uploaded file from the request, allowing the AssignmentService to handle the file upload and associate it with the submission when a student submits an assignment. The file is optional, but if provided, it will be processed and stored accordingly by the service.
        );

        return $this->successResponse(
            new SubmissionResource($result['submission']),
            'Assignment submitted successfully.',
            200
        );
    }

    public function index(): JsonResponse
    {
        $assignments = $this->assignmentService->getAll();
        return $this->successResponse(
            AssignmentResource::collection($assignments),
            'Assignments retrieved successfully.'
        );
    }

    public function show(Assignment $assignment): JsonResponse
    {
        $this->authorize('view', $assignment);
        return $this->successResponse(
            new AssignmentResource($assignment),
            'Assignment retrieved successfully.'
        );
    }
}
