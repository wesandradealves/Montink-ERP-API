<?php

namespace App\Modules\Products\Api\Controllers;

use App\Common\Base\BaseApiController;
use App\Common\Enums\ResponseMessage;
use App\Modules\Products\Api\Requests\CreateProductRequest;
use App\Modules\Products\Api\Requests\UpdateProductRequest;
use App\Modules\Products\UseCases\ProductsUseCase;
use Illuminate\Http\Request;

class ProductController extends BaseApiController
{
    
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
    public function index(Request $request)
    {
        return $this->handleUseCaseExecution(function() use ($request) {
            $products = $this->productsUseCase->list([
                'only_active' => $request->boolean('only_active'),
                'search' => $request->get('search'),
            ]);
            return $products;
        });
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
    public function show(int $id)
    {
        return $this->handleUseCaseExecution(function() use ($id) {
            $product = $this->productsUseCase->find($id);
            
            if (!$product) {
                throw new \InvalidArgumentException('Produto não encontrado');
            }
            
            return $product;
        });
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
    public function store(CreateProductRequest $request)
    {
        return $this->handleUseCaseCreation(function() use ($request) {
            return $this->productsUseCase->create($request->validated());
        }, ResponseMessage::PRODUCT_CREATED->get());
    }

    /**
     * @OA\Patch(
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
    public function update(UpdateProductRequest $request, int $id)
    {
        return $this->handleUseCaseExecution(function() use ($request, $id) {
            return $this->productsUseCase->update($id, $request->validated());
        });
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
    public function destroy(int $id)
    {
        return $this->handleUseCaseExecution(function() use ($id) {
            $this->productsUseCase->delete($id);
            return null;
        });
    }
}