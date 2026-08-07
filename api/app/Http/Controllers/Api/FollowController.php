<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FollowService;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function __construct(
        private FollowService $followService
    ) {}

    public function store(Request $request, User $user) {
        $this->followService->follow($request->user(), $user);

        return response()->json([
            'message' => 'Usuário seguido com sucesso',
        ], 201);
    }
    
    public function destroy(Request $request, User $user) {
        $this->followService->unfollow($request->user(), $user);

        return response()->noContent();
    }
}
