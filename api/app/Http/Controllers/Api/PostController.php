<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Resources\PostResource;
use App\Services\PostService;

class PostController extends Controller
{
    public function __construct(
        private PostService $postService
    ) {}


    public function index() {
        $posts = $this->postService->paginate();

        return PostResource::collection($posts);
    }

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

    public function show(Post $post): PostResource {
        $post = $this->postService->get($post);

        return new PostResource($post);
    }

    public function destroy(Post $post)
    {
        Gate::authorize('delete', $post);

        $this->postService->delete($post);

        return response()->noContent();
    }

}
