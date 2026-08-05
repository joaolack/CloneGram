<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Resources\PostResource;
use App\Services\PostService;

class PostController extends Controller
{
    public function __construct(
        private PostService $postService
    ) {}

    public function store(StorePostRequest $request)
    {
        $post = $this->postService->create(
            $request->user(),
            $request->validated()
        );

        return (new PostResource($post->load('user')))
            ->response()
            ->setStatusCode(201);
    }

}
