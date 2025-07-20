<?php

namespace App\Common\Base;

use App\Http\Controllers\Controller;
use App\Common\Traits\ApiResponseTrait;
use App\Common\Enums\ResponseMessage;
use App\Common\Exceptions\ResourceNotFoundException;
use App\Common\Exceptions\AuthenticationException;
use Illuminate\Http\JsonResponse;

abstract class BaseApiController extends Controller
{
    use ApiResponseTrait;

    protected function handleUseCaseExecution(callable $callback, ?string $successMessage = null, int $statusCode = 200): JsonResponse
    {
        try {
            $result = $callback();
            $message = $successMessage ?? ResponseMessage::OPERATION_SUCCESS->get();
            return $this->successResponse($result, $message, $statusCode);
        } catch (ResourceNotFoundException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        } catch (AuthenticationException $e) {
            return $this->errorResponse($e->getMessage(), 401);
        } catch (\InvalidArgumentException $e) {
            return $this->errorResponse($e->getMessage(), 422);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    protected function handleUseCaseExecutionWithMessage(callable $callback, string $successMessage): JsonResponse
    {
        return $this->handleUseCaseExecution($callback, $successMessage);
    }

    protected function handleUseCaseCreation(callable $callback, ?string $successMessage = null): JsonResponse
    {
        return $this->handleUseCaseExecution($callback, $successMessage ?? ResponseMessage::DEFAULT_CREATED->get(), 201);
    }
}