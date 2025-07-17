<?php

namespace App\Modules\Products\Api\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Modules\Products\Api\Requests\CreateProductRequest;
use App\Modules\Products\Api\Requests\UpdateProductRequest;
use App\Modules\Products\UseCases\ProductsUseCase;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use ApiResponseTrait;
    
    public function __construct(
        private readonly ProductsUseCase $productsUseCase,
    ) {
    }

    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="Listar produtos",
     *     description="Retorna lista de produtos com opção de filtros",
     *     operationId="getProducts",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="only_active",
     *         in="query",
     *         description="Filtrar apenas produtos ativos",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Buscar por nome ou SKU",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de produtos retornada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Product")),
     *             @OA\Property(property="meta", type="object", @OA\Property(property="total", type="integer", example=10))
     *         )
     *     )
     * )
     */
    public function index(Request $request): array
    {
        $products = $this->productsUseCase->list([
            'only_active' => $request->boolean('only_active'),
            'search' => $request->get('search'),
        ]);

        return $this->successListResponse($products);
    }

    /**
     * @OA\Get(
     *     path="/api/products/{id}",
     *     summary="Buscar produto por ID",
     *     description="Retorna um produto específico pelo ID",
     *     operationId="getProductById",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do produto",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Produto encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/Product"),
     *             @OA\Property(property="message", type="string", example="Produto encontrado com sucesso")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Produto não encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Produto não encontrado")
     *         )
     *     )
     * )
     */
    public function show(int $id): array
    {
        $product = $this->productsUseCase->find($id);

        if (!$product) {
            return $this->errorResponse('Produto não encontrado', 404);
        }

        return $this->successResponse($product);
    }

    /**
     * @OA\Post(
     *     path="/api/products",
     *     summary="Criar novo produto",
     *     description="Cria um novo produto no sistema",
     *     operationId="createProduct",
     *     tags={"Products"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Dados do produto",
     *         @OA\JsonContent(
     *             required={"name", "price", "sku"},
     *             @OA\Property(property="name", type="string", maxLength=255, example="Notebook Dell"),
     *             @OA\Property(property="description", type="string", example="Notebook Dell Inspiron 15"),
     *             @OA\Property(property="price", type="number", format="float", minimum=0, example=2999.90),
     *             @OA\Property(property="sku", type="string", maxLength=50, example="NTB-DELL-001"),
     *             @OA\Property(property="active", type="boolean", example=true),
     *             @OA\Property(property="variations", type="object", example={"cor": "preto", "tamanho": "15 polegadas"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Produto criado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/Product"),
     *             @OA\Property(property="message", type="string", example="Produto criado com sucesso")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="SKU já existe no sistema")
     *         )
     *     )
     * )
     */
    public function store(CreateProductRequest $request): array
    {
        try {
            $product = $this->productsUseCase->create($request->validated());
            return $this->successResponse($product, 'Produto criado com sucesso', 201);
        } catch (\InvalidArgumentException $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/products/{id}",
     *     summary="Atualizar produto",
     *     description="Atualiza um produto existente",
     *     operationId="updateProduct",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do produto",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Dados do produto para atualização",
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", maxLength=255, example="Notebook Dell Atualizado"),
     *             @OA\Property(property="description", type="string", example="Notebook Dell Inspiron 15 - Nova versão"),
     *             @OA\Property(property="price", type="number", format="float", minimum=0, example=3299.90),
     *             @OA\Property(property="sku", type="string", maxLength=50, example="NTB-DELL-001-V2"),
     *             @OA\Property(property="active", type="boolean", example=true),
     *             @OA\Property(property="variations", type="object", example={"cor": "preto", "tamanho": "15 polegadas", "ram": "16GB"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Produto atualizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/Product"),
     *             @OA\Property(property="message", type="string", example="Produto atualizado com sucesso")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Produto não encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Produto não encontrado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="SKU já existe no sistema")
     *         )
     *     )
     * )
     */
    public function update(UpdateProductRequest $request, int $id): array
    {
        try {
            $product = $this->productsUseCase->update($id, $request->validated());
            return $this->successResponse($product, 'Produto atualizado com sucesso');
        } catch (\InvalidArgumentException $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/products/{id}",
     *     summary="Excluir produto",
     *     description="Remove um produto do sistema",
     *     operationId="deleteProduct",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do produto",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Produto excluído com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="null"),
     *             @OA\Property(property="message", type="string", example="Produto excluído com sucesso")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Produto não encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Produto não encontrado")
     *         )
     *     )
     * )
     */
    public function destroy(int $id): array
    {
        try {
            $this->productsUseCase->delete($id);
            return $this->successResponse(null, 'Produto excluído com sucesso');
        } catch (\InvalidArgumentException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }
    }
}