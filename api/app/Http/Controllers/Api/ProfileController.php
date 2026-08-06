<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\ProfileService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct(
        private ProfileService $profileService
    ) {}

    public function me(Request $request): UserResource {
        $user = $this->profileService->getOwnProfile($request->user());

        return new UserResource($user);
    }

    public function show(User $user): UserResource {
        $user = $this->profileService->getOtherProfile($user);

        return new UserResource($user);
    }

    public function update(UpdateProfileRequest $request): UserResource {
        $user = $this->profileService->update($request->user(), $request->validated());

        return new UserResource($user);
    }
}
