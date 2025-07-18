<?php

namespace App\Common\Base;

use App\Http\Controllers\Controller;
use App\Common\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;

abstract class BaseApiController extends Controller
{
    use ApiResponseTrait;

    protected function handleUseCaseExecution(callable $callback): JsonResponse
    {
        try {
            $result = $callback();
            return $this->successResponse($result, 'Operação realizada com sucesso');
        } catch (\InvalidArgumentException $e) {
            return $this->errorResponse($e->getMessage(), 422);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    protected function handleUseCaseExecutionWithMessage(callable $callback, string $successMessage): JsonResponse
    {
        try {
            $result = $callback();
            return $this->successResponse($result, $successMessage);
        } catch (\InvalidArgumentException $e) {
            return $this->errorResponse($e->getMessage(), 422);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    protected function handleUseCaseCreation(callable $callback, string $successMessage = 'Criado com sucesso'): JsonResponse
    {
        try {
            $result = $callback();
            return $this->successResponse($result, $successMessage, 201);
        } catch (\InvalidArgumentException $e) {
            return $this->errorResponse($e->getMessage(), 422);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}