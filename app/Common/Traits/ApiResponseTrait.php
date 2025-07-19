<?php

namespace App\Common\Traits;

use Illuminate\Http\JsonResponse;
use App\Common\Enums\ResponseMessage;

trait ApiResponseTrait
{
    public function successResponse($data = null, ?string $message = null, int $statusCode = 200): JsonResponse
    {
        $response = [
            'data' => $data,
            'message' => $message ?? ResponseMessage::DEFAULT_SUCCESS->get(),
        ];

        return response()->json($response, $statusCode);
    }

    public function successListResponse($data = [], int $total = 0, ?string $message = null): JsonResponse
    {
        $response = [
            'data' => $data,
            'message' => $message ?? ResponseMessage::DEFAULT_SUCCESS->get(),
            'meta' => [
                'total' => $total,
            ],
        ];

        return response()->json($response, 200);
    }

    public function errorResponse(?string $message = null, int $statusCode = 500): JsonResponse
    {
        return response()->json([
            'error' => $message ?? ResponseMessage::DEFAULT_ERROR->get(),
        ], $statusCode);
    }
}