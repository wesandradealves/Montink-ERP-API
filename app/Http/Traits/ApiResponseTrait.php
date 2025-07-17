<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    protected function successResponse($data, string $message = null, int $status = 200): array
    {
        $response = ['data' => $data];
        
        if ($message) {
            $response['message'] = $message;
        }
        
        return $response;
    }

    protected function successListResponse(array $data): array
    {
        return [
            'data' => $data,
            'meta' => [
                'total' => count($data),
            ],
        ];
    }

    protected function errorResponse(string $message, int $status = 400): array
    {
        return ['message' => $message];
    }
}