<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HealthController extends Controller
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
    public function check(): array
    {
        return [
            'status' => 'healthy',
            'timestamp' => date('c'),
            'version' => '0.4.0'
        ];
    }
}