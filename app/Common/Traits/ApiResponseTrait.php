<?php

namespace App\Common\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    public function successResponse($data = null, string $message = 'Success', int $statusCode = 200): JsonResponse
    {
        $response = [
            'data' => $data,
            'message' => $message,
        ];

        return response()->json($response, $statusCode);
    }

    public function successListResponse($data = [], int $total = 0, string $message = 'Success'): JsonResponse
    {
        $response = [
            'data' => $data,
            'message' => $message,
            'meta' => [
                'total' => $total,
            ],
        ];

        return response()->json($response, 200);
    }

    public function errorResponse(string $message = 'Error', int $statusCode = 500): JsonResponse
    {
        return response()->json([
            'error' => $message,
        ], $statusCode);
    }
}