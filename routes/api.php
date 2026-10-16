<?php

use App\Support\ApiResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/health', function () {
        $dbStatus = 'connected';
        $dbError = null;

        try {
            DB::connection()->getPdo();
        } catch (\Throwable $e) {
            $dbStatus = 'disconnected';
            $dbError = $e->getMessage();
        }

        $data = [
            'status' => 'healthy',
            'timestamp' => now()->toIso8601String(),
            'services' => [
                'database' => [
                    'status' => $dbStatus,
                    'error' => $dbError,
                ],
            ],
        ];

        return ApiResponse::success($data, 'System is healthy');
    });
});
