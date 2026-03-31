<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Comment\StoreCommentRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Services\CommentService;
use App\Traits\ApiResponseTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CommentController extends Controller
{
    use ApiResponseTrait;
    use AuthorizesRequests;

    public function __construct(
        private CommentService $commentService
    ) {} // this constructor injects the CommentService into the controller, allowing it to be used for handling comment-related operations.

    public function store(StoreCommentRequest $request): JsonResponse
        {
        $comment = $this->commentService->create($request->validated(), $request->user()->id); // this creates a new comment using the CommentService with the validated request data and the authenticated user's ID as the author of the comment.
            return $this->successResponse(
                new CommentResource($comment),
                'Comment created successfully.',
                201
            );
        }
    
    public function index(Request $request): JsonResponse
        {
            $request->validate([
                'commentable_type' => 'required|string|in:course,lesson,assignment',
                'commentable_id' => 'required|integer',
            ]); // this validates the incoming request to ensure that it includes the required parameters for filtering comments based on the type and ID of the entity being commented on (course, lesson, or assignment).

            $comments = $this->commentService->getFor($request->commentable_type, $request->commentable_id); // this retrieves the comments for the specified entity using the CommentService, passing the commentable type and ID from the request.
            return $this->successResponse(
                CommentResource::collection($comments),
                'Comments retrieved successfully.'
            );
        }
}
