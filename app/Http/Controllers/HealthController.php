<?php

namespace App\Http\Controllers;

use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class HealthController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $dbStatus = 'connected';
        $dbError = null;

        try {
            DB::connection()->getPdo();
        } catch (\Throwable $e) {
            $dbStatus = 'disconnected';
            // Only expose error message in local/testing
            $dbError = app()->environment(['local', 'testing']) ? $e->getMessage() : null;
        }

        $healthy = $dbStatus === 'connected';

        $data = [
            'status' => $healthy ? 'healthy' : 'unhealthy',
            'timestamp' => now()->toIso8601String(),
            'services' => [
                'database' => [
                    'status' => $dbStatus,
                    'error' => $dbError,
                ],
            ],
        ];

        return $healthy
            ? ApiResponse::success($data, 'System is healthy')
            : ApiResponse::error('System is unhealthy', 503, data: $data);
    }
}
