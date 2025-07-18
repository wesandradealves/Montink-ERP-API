<?php

namespace App\Common\Base;

use App\Http\Controllers\Controller;
use App\Common\Traits\ApiResponseTrait;
use App\Common\Enums\ResponseMessage;
use App\Common\Exceptions\ResourceNotFoundException;
use Illuminate\Http\JsonResponse;

abstract class BaseApiController extends Controller
{
    use ApiResponseTrait;

    protected function handleUseCaseExecution(callable $callback): JsonResponse
    {
        try {
            $result = $callback();
            return $this->successResponse($result, ResponseMessage::OPERATION_SUCCESS->get());
        } catch (ResourceNotFoundException $e) {
            return $this->errorResponse($e->getMessage(), 404);
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
        } catch (ResourceNotFoundException $e) {
            return $this->errorResponse($e->getMessage(), 404);
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
        } catch (ResourceNotFoundException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        } catch (\InvalidArgumentException $e) {
            return $this->errorResponse($e->getMessage(), 422);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}