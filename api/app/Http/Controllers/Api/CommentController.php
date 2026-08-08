<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Post;
use App\Services\CommentService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CommentController extends Controller
{
    public function __construct(
        private CommentService $commentService
    ) {}

    public function index(Post $post): AnonymousResourceCollection {
        $comments = $this->commentService->getByPost($post);

        return CommentResource::collection($comments);
    }

    public function store(StoreCommentRequest $request, Post $post) {
        $comment = $this->commentService->create(
            $request->user(),
            $post,
            $request->validated()
        );

        return (new CommentResource($comment))
            ->response()
            ->setStatusCode(201);
    }
}
