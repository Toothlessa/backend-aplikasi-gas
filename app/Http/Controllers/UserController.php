<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;
use App\Http\Requests\User\UserRegisterRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Http\Requests\User\UserLoginRequest;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected $service;

    public function __construct(UserService $service) {
        $this->service = $service;
    }

    public function register(UserRegisterRequest $request)
    {
        $data = $request->validated();

        $user = $this->service->register($data);

        return (new UserResource($user))->response()->setStatusCode(201);
        
    }

    public function update(UserUpdateRequest $request)
    {
        Auth::user();
        $data = $request->validated();

        $user = $this->service->update($data);

        return (new UserResource($user))->response()->setStatusCode(200);
    }

    public function login(UserLoginRequest $request)
    {
        $data = $request->validated();

        $user = $this->service->login($data);

        return (new UserResource($user))->response()->setStatusCode(200);
    }

    public function get(Request $request): UserResource
    {
        $user = Auth::user();
        return new UserResource($user);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->service->logout();

        return response()->json([
            "data" => true
        ])->setStatusCode(200);
    }
}