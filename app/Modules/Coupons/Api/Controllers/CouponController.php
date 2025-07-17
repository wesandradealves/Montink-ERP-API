<?php

namespace App\Modules\Coupons\Api\Controllers;

use App\Common\Base\BaseApiController;
use App\Modules\Coupons\Api\Requests\CreateCouponRequest;
use App\Modules\Coupons\Api\Requests\UpdateCouponRequest;
use App\Modules\Coupons\Api\Requests\ValidateCouponRequest;
use App\Modules\Coupons\DTOs\CreateCouponDTO;
use App\Modules\Coupons\DTOs\UpdateCouponDTO;
use App\Modules\Coupons\DTOs\ValidateCouponDTO;
use App\Modules\Coupons\UseCases\CouponsUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Coupons",
 *     description="Endpoints para gestão de cupons de desconto"
 * )
 */
class CouponController extends BaseApiController
{
    public function __construct(
        private readonly CouponsUseCase $couponsUseCase
    ) {}

    /**
     * @OA\Get(
     *     path="/api/coupons",
     *     tags={"Coupons"},
     *     summary="Listar cupons",
     *     @OA\Parameter(
     *         name="active",
     *         in="query",
     *         description="Filtrar por cupons ativos",
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="valid",
     *         in="query",
     *         description="Filtrar apenas cupons válidos",
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Buscar por código ou descrição",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de cupons",
     *         @OA\JsonContent(ref="#/components/schemas/ApiListResponse")
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        return $this->handleUseCaseExecution(function () use ($request) {
            $filters = $request->only(['active', 'valid', 'search']);
            if (isset($filters['active'])) {
                $filters['active'] = filter_var($filters['active'], FILTER_VALIDATE_BOOLEAN);
            }
            if (isset($filters['valid'])) {
                $filters['valid'] = filter_var($filters['valid'], FILTER_VALIDATE_BOOLEAN);
            }
            return $this->couponsUseCase->listCoupons($filters);
        });
    }

    /**
     * @OA\Post(
     *     path="/api/coupons",
     *     tags={"Coupons"},
     *     summary="Criar novo cupom",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"code", "type", "value"},
     *             @OA\Property(property="code", type="string", example="DESCONTO10"),
     *             @OA\Property(property="description", type="string", example="Cupom de 10% de desconto"),
     *             @OA\Property(property="type", type="string", enum={"fixed", "percentage"}, example="percentage"),
     *             @OA\Property(property="value", type="number", format="float", example=10.00),
     *             @OA\Property(property="minimum_value", type="number", format="float", example=50.00),
     *             @OA\Property(property="usage_limit", type="integer", example=100),
     *             @OA\Property(property="valid_from", type="string", format="date", example="2025-01-01"),
     *             @OA\Property(property="valid_until", type="string", format="date", example="2025-12-31"),
     *             @OA\Property(property="active", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Cupom criado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/ApiResponse")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationError")
     *     )
     * )
     */
    public function store(CreateCouponRequest $request): JsonResponse
    {
        return $this->handleUseCaseExecution(function () use ($request) {
            $validated = $request->validated();
            $dto = new CreateCouponDTO(
                code: $validated['code'],
                description: $validated['description'] ?? null,
                type: $validated['type'],
                value: $validated['value'],
                minimum_value: $validated['minimum_value'] ?? null,
                usage_limit: $validated['usage_limit'] ?? null,
                valid_from: $validated['valid_from'] ?? null,
                valid_until: $validated['valid_until'] ?? null,
                active: $validated['active'] ?? true
            );
            return $this->couponsUseCase->createCoupon($dto);
        });
    }

    /**
     * @OA\Get(
     *     path="/api/coupons/{id}",
     *     tags={"Coupons"},
     *     summary="Buscar cupom por ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dados do cupom",
     *         @OA\JsonContent(ref="#/components/schemas/ApiResponse")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cupom não encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        return $this->handleUseCaseExecution(function () use ($id) {
            return $this->couponsUseCase->getCouponById($id);
        });
    }

    /**
     * @OA\Get(
     *     path="/api/coupons/code/{code}",
     *     tags={"Coupons"},
     *     summary="Buscar cupom por código",
     *     @OA\Parameter(
     *         name="code",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dados do cupom",
     *         @OA\JsonContent(ref="#/components/schemas/ApiResponse")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cupom não encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *     )
     * )
     */
    public function showByCode(string $code): JsonResponse
    {
        return $this->handleUseCaseExecution(function () use ($code) {
            return $this->couponsUseCase->getCouponByCode($code);
        });
    }

    /**
     * @OA\Patch(
     *     path="/api/coupons/{id}",
     *     tags={"Coupons"},
     *     summary="Atualizar cupom",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="type", type="string", enum={"fixed", "percentage"}),
     *             @OA\Property(property="value", type="number", format="float"),
     *             @OA\Property(property="minimum_value", type="number", format="float"),
     *             @OA\Property(property="usage_limit", type="integer"),
     *             @OA\Property(property="valid_from", type="string", format="date"),
     *             @OA\Property(property="valid_until", type="string", format="date"),
     *             @OA\Property(property="active", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cupom atualizado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/ApiResponse")
     *     )
     * )
     */
    public function update(UpdateCouponRequest $request, int $id): JsonResponse
    {
        return $this->handleUseCaseExecution(function () use ($request, $id) {
            $dto = new UpdateCouponDTO(...$request->validated());
            return $this->couponsUseCase->updateCoupon($id, $dto);
        });
    }

    /**
     * @OA\Delete(
     *     path="/api/coupons/{id}",
     *     tags={"Coupons"},
     *     summary="Excluir cupom",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cupom excluído com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/ApiResponse")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cupom não encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *     )
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        return $this->handleUseCaseExecution(function () use ($id) {
            $this->couponsUseCase->deleteCoupon($id);
            return ['message' => 'Cupom excluído com sucesso'];
        });
    }

    /**
     * @OA\Post(
     *     path="/api/coupons/validate",
     *     tags={"Coupons"},
     *     summary="Validar cupom de desconto",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"code", "value"},
     *             @OA\Property(property="code", type="string", example="DESCONTO10"),
     *             @OA\Property(property="value", type="number", format="float", example=100.00)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Resultado da validação",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="valid", type="boolean"),
     *                 @OA\Property(property="message", type="string"),
     *                 @OA\Property(property="discount", type="number"),
     *                 @OA\Property(property="formatted_discount", type="string"),
     *                 @OA\Property(property="final_value", type="number"),
     *                 @OA\Property(property="formatted_final_value", type="string")
     *             )
     *         )
     *     )
     * )
     */
    public function validateCoupon(ValidateCouponRequest $request): JsonResponse
    {
        return $this->handleUseCaseExecution(function () use ($request) {
            $dto = new ValidateCouponDTO(...$request->validated());
            return $this->couponsUseCase->validateCoupon($dto);
        });
    }
}