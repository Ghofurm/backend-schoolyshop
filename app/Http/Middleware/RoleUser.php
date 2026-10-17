<?php

namespace App\Http\Middleware;

use App\Enums\StatusAkun;
use App\Models\User;
use App\Support\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleUser
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

        if (! $user instanceof User || ! $user->currentAccessToken()?->can('user')) {
            return ApiResponse::error('Akses ditolak.', 403);
        }

        if ($user->status_akun !== StatusAkun::AKTIF) {
            return ApiResponse::error('Akun tidak aktif.', 403);
        }

        $request->setUserResolver(fn () => $user);

        return $next($request);
    }
}
