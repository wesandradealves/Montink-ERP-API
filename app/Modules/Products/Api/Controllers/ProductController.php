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

    public function index(Request $request): array
    {
        $products = $this->productsUseCase->list([
            'only_active' => $request->boolean('only_active'),
            'search' => $request->get('search'),
        ]);

        return $this->successListResponse($products);
    }

    public function show(int $id): array
    {
        $product = $this->productsUseCase->find($id);

        if (!$product) {
            return $this->errorResponse('Produto não encontrado', 404);
        }

        return $this->successResponse($product);
    }

    public function store(CreateProductRequest $request): array
    {
        try {
            $product = $this->productsUseCase->create($request->validated());
            return $this->successResponse($product, 'Produto criado com sucesso', 201);
        } catch (\InvalidArgumentException $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }

    public function update(UpdateProductRequest $request, int $id): array
    {
        try {
            $product = $this->productsUseCase->update($id, $request->validated());
            return $this->successResponse($product, 'Produto atualizado com sucesso');
        } catch (\InvalidArgumentException $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }

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