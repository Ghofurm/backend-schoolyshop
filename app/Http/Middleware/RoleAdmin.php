<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use App\Support\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $guard = Auth::guard('sanctum');
        $guard->setRequest($request);
        $guard->forgetUser();

        $user = $guard->user();

        if (! $user) {
            return ApiResponse::error('Tidak terautentikasi.', 401);
        }

        if (! $user instanceof Admin || ! $user->currentAccessToken()?->can('admin')) {
            return ApiResponse::error('Akses ditolak.', 403);
        }

        $request->setUserResolver(fn () => $user);

        return $next($request);
    }
}
