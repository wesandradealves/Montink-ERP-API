<?php

namespace App\Http\Controllers;

use App\Common\Base\BaseApiController;
use App\Common\Enums\ResponseMessage;
use Illuminate\Http\JsonResponse;

class HealthController extends BaseApiController
{
    /**
     * @OA\Get(
     *     path="/api/health",
     *     summary="Verificação de saúde da API",
     *     description="Endpoint para verificar se a API está funcionando corretamente",
     *     operationId="healthCheck",
     *     tags={"Health"},
     *     @OA\Response(
     *         response=200,
     *         description="API funcionando corretamente",
     *         @OA\JsonContent(ref="#/components/schemas/HealthCheck")
     *     )
     * )
     */
    public function check(): JsonResponse
    {
        $data = [
            'status' => 'healthy',
            'timestamp' => now()->toIso8601String(),
            'version' => config('app.version', '1.1.0'),
            'environment' => config('app.env'),
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache()
        ];

        return $this->successResponse($data);
    }

    /**
     * Verifica conexão com banco de dados
     */
    private function checkDatabase(): string
    {
        try {
            \DB::connection()->getPdo();
            return 'connected';
        } catch (\Exception $e) {
            return 'disconnected';
        }
    }

    /**
     * Verifica conexão com cache
     */
    private function checkCache(): string
    {
        try {
            cache()->store()->put('health_check', true, 1);
            cache()->store()->forget('health_check');
            return 'working';
        } catch (\Exception $e) {
            return 'not working';
        }
    }
}