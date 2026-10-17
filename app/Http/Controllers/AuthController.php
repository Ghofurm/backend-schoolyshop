<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserAuthResource;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->authService->registerUser($request->validated());

        return ApiResponse::success(
            data: new UserResource($user),
            message: 'Registrasi berhasil.',
            code: 201
        );
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->loginUser(
            email: $request->validated('email'),
            password: $request->validated('password')
        );

        return ApiResponse::success(
            data: new UserAuthResource($result['user'], $result['token']),
            message: 'Login berhasil.'
        );
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logoutUser($request->user());

        return ApiResponse::success(
            message: 'Logout berhasil.'
        );
    }
}
