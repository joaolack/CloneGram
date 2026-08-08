<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Services\SearchService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __construct(
        private SearchService $searchService
    ) {}

    public function index(Request $request) {
        $validated = $request->validate([
            'search' => [
                'nullable',
                'string',
                'max:100',
            ],
        ]);

        $users = $this->searchService->search(
            $validated['search'] ?? null
        );

        return UserResource::collection($users);
    }
}
