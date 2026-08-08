<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Services\LikeService;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function __construct(
        private LikeService $likeService
    ) {}

    public function store(Request $request, Post $post) {
        $this->likeService->like($request->user(), $post);

        return response()->json([
            'message' => 'Post curtido com sucesso',
        ], 201);
    }

    public function destroy(Request $request, Post $post) {
        $this->likeService->unlike($request->user(), $post);

        return response()->noContent();
    }
}
