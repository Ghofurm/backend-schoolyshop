<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Resources\AdminAuthResource;
use App\Http\Resources\AdminResource;
use App\Services\AuthService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminAuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->loginAdmin(
            email: $request->validated('email'),
            password: $request->validated('password')
        );

        return ApiResponse::success(
            data: new AdminAuthResource($result['admin'], $result['token']),
            message: 'Login berhasil.'
        );
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logoutAdmin($request->user());

        return ApiResponse::success(
            message: 'Logout berhasil.'
        );
    }

    public function me(Request $request): JsonResponse
    {
        return ApiResponse::success(
            data: new AdminResource($request->user()),
            message: 'Data admin berhasil diambil.'
        );
    }
}
