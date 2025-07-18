<?php

namespace App\Modules\Address\Api\Controllers;

use App\Common\Base\BaseApiController;
use App\Modules\Address\Services\ViaCepService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddressController extends BaseApiController
{
    public function __construct(
        private ViaCepService $viaCepService
    ) {}

    /**
     * @OA\Get(
     *     path="/api/address/cep/{cep}",
     *     summary="Buscar endereço por CEP",
     *     description="Consulta endereço através da API ViaCEP",
     *     operationId="getAddressByCep",
     *     tags={"Address"},
     *     @OA\Parameter(
     *         name="cep",
     *         in="path",
     *         description="CEP para consulta (8 dígitos)",
     *         required=true,
     *         @OA\Schema(type="string", pattern="^[0-9]{8}$", example="01310100")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Endereço encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/Address"),
     *             @OA\Property(property="message", type="string", example="Endereço encontrado com sucesso")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="CEP não encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="CEP não encontrado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="CEP inválido",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="CEP deve conter 8 dígitos")
     *         )
     *     )
     * )
     */
    public function getByCep(string $cep): JsonResponse
    {
        return $this->handleUseCaseExecution(function() use ($cep) {
            $address = $this->viaCepService->getAddressByCep($cep);
            
            if (!$address) {
                throw new \InvalidArgumentException('CEP não encontrado');
            }
            
            return $address;
        });
    }

    /**
     * @OA\Post(
     *     path="/api/address/validate-cep",
     *     summary="Validar CEP",
     *     description="Valida se um CEP existe na base ViaCEP",
     *     operationId="validateCep",
     *     tags={"Address"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"cep"},
     *             @OA\Property(property="cep", type="string", example="01310100", description="CEP para validação")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Resultado da validação",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="valid", type="boolean", example=true),
     *                 @OA\Property(property="cep", type="string", example="01310100")
     *             ),
     *             @OA\Property(property="message", type="string", example="CEP validado com sucesso")
     *         )
     *     )
     * )
     */
    public function validateCep(Request $request): JsonResponse
    {
        return $this->handleUseCaseExecution(function() use ($request) {
            $cep = $request->input('cep');
            $isValid = $this->viaCepService->validateCep($cep);
            
            return [
                'valid' => $isValid,
                'cep' => $cep,
            ];
        });
    }
}