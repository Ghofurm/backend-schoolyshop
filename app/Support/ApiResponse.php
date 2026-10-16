<?php

namespace App\Support;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

class ApiResponse
{
    /**
     * Build standard success response.
     *
     * @param mixed $data
     * @param string $message
     * @param int $code
     * @param array|null $meta
     * @return JsonResponse
     */
    public static function success(mixed $data = null, string $message = 'Success', int $code = 200, ?array $meta = null): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => $meta,
            'errors' => null,
        ], $code);
    }

    /**
     * Build standard error response.
     *
     * @param string $message
     * @param int $code
     * @param array|null $errors
     * @param mixed $data
     * @return JsonResponse
     */
    public static function error(string $message = 'Error', int $code = 400, ?array $errors = null, mixed $data = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => $data,
            'meta' => null,
            'errors' => $errors,
        ], $code);
    }

    /**
     * Build standard paginated response.
     *
     * @param LengthAwarePaginator $paginator
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    public static function paginated(LengthAwarePaginator $paginator, string $message = 'Success', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
            'errors' => null,
        ], $code);
    }
}
