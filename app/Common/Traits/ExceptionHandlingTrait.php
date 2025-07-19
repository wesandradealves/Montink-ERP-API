<?php

namespace App\Common\Traits;

use App\Common\Enums\ResponseMessage;
use App\Common\Exceptions\ResourceNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

trait ExceptionHandlingTrait
{
    /**
     * Tratar exceção e retornar resposta apropriada
     */
    protected function handleException(\Exception $e, string $context = ''): JsonResponse
    {
        // Log do erro
        $this->logException($e, $context);
        
        // Determinar código HTTP
        $statusCode = $this->getExceptionStatusCode($e);
        
        // Determinar mensagem
        $message = $this->getExceptionMessage($e);
        
        return response()->json([
            'error' => $message,
            'type' => class_basename($e)
        ], $statusCode);
    }

    /**
     * Log de exceção com contexto
     */
    protected function logException(\Exception $e, string $context = ''): void
    {
        $logData = [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ];
        
        if ($context) {
            $logData['context'] = $context;
        }
        
        Log::error('Exception: ' . class_basename($e), $logData);
    }

    /**
     * Determinar código HTTP baseado na exceção
     */
    protected function getExceptionStatusCode(\Exception $e): int
    {
        if ($e instanceof ResourceNotFoundException) {
            return 404;
        }
        
        if ($e instanceof \InvalidArgumentException) {
            return 400;
        }
        
        if ($e instanceof \UnauthorizedException) {
            return 401;
        }
        
        if ($e instanceof \ForbiddenException) {
            return 403;
        }
        
        // Verificar se a exceção tem código HTTP
        $code = $e->getCode();
        if ($code >= 400 && $code < 600) {
            return $code;
        }
        
        return 500;
    }

    /**
     * Determinar mensagem baseada na exceção
     */
    protected function getExceptionMessage(\Exception $e): string
    {
        // Se for uma exceção conhecida com mensagem do ResponseMessage
        if (method_exists($e, 'getResponseMessage')) {
            return $e->getResponseMessage();
        }
        
        // Para exceções de validação
        if ($e instanceof \InvalidArgumentException) {
            return $e->getMessage();
        }
        
        // Para ResourceNotFoundException
        if ($e instanceof ResourceNotFoundException) {
            return $e->getMessage() ?: ResponseMessage::RESOURCE_NOT_FOUND->get();
        }
        
        // Para outras exceções em produção, não expor detalhes
        if (app()->environment('production')) {
            return ResponseMessage::OPERATION_ERROR->get();
        }
        
        return $e->getMessage();
    }

    /**
     * Executar callback com tratamento de exceção
     */
    protected function executeWithExceptionHandling(callable $callback, string $context = '')
    {
        try {
            return $callback();
        } catch (\Exception $e) {
            return $this->handleException($e, $context);
        }
    }
}