<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Services\HomeService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct(
        private HomeService $homeService
    ) {}

    public function index(Request $request) {
        $data = $this->homeService->getHome($request->user());

        return response()->json([
            'posts' => PostResource::collection($data['posts']),
            'suggestions' => UserResource::collection($data['suggestions']),
        ]);
    }
}
