<?php

namespace App\Modules\Cart\UseCases;

use App\Common\Exceptions\ResourceNotFoundException;
use App\Common\Services\SessionService;
use App\Modules\Cart\DTOs\AddToCartDTO;
use App\Modules\Cart\DTOs\CartDTO;
use App\Modules\Cart\DTOs\CartItemDTO;
use App\Modules\Cart\Models\CartItem;
use App\Modules\Cart\Services\ShippingService;
use App\Modules\Products\Models\Product;
use App\Modules\Stock\Services\StockValidationService;
use App\Common\Base\BaseUseCase;

class CartUseCase extends BaseUseCase
{
    public function __construct(
        private ShippingService $shippingService,
        private StockValidationService $stockValidationService,
        private SessionService $sessionService
    ) {}
    public function addToCart(AddToCartDTO $dto): CartDTO
    {
        $product = Product::findOrFail($dto->productId);
        
        $this->stockValidationService->validateStock($product, $dto->quantity, $dto->variations);
        
        $sessionId = $this->sessionService->getCurrentId();
        
        $existingItem = CartItem::where('session_id', $sessionId)
            ->where('product_id', $dto->productId)
            ->when($dto->variations, function ($query) use ($dto) {
                return $query->where('variations', json_encode($dto->variations));
            })
            ->first();

        if ($existingItem) {
            $newQuantity = $existingItem->quantity + $dto->quantity;
            $this->stockValidationService->validateStock($product, $newQuantity, $dto->variations);
            
            $existingItem->update(['quantity' => $newQuantity]);
        } else {
            CartItem::create([
                'session_id' => $sessionId,
                'product_id' => $dto->productId,
                'quantity' => $dto->quantity,
                'price' => $product->price,
                'variations' => $dto->variations,
            ]);
        }

        return $this->getCart();
    }

    public function removeFromCart(int $itemId): CartDTO
    {
        $sessionId = $this->sessionService->getCurrentId();
        
        CartItem::where('session_id', $sessionId)
            ->where('id', $itemId)
            ->delete();

        return $this->getCart();
    }

    public function updateQuantity(int $itemId, int $quantity): CartDTO
    {
        $sessionId = $this->sessionService->getCurrentId();
        
        $cartItem = CartItem::where('session_id', $sessionId)
            ->where('id', $itemId)
            ->firstOrFail();

        $this->stockValidationService->validateStock($cartItem->product, $quantity, $cartItem->variations);

        $cartItem->update(['quantity' => $quantity]);

        return $this->getCart();
    }

    public function getCart(): CartDTO
    {
        $sessionId = $this->sessionService->getCurrentId();
        
        $cartItems = CartItem::with('product')
            ->where('session_id', $sessionId)
            ->get();

        $items = $cartItems->map(function ($item) {
            return new CartItemDTO(
                id: $item->id,
                productId: $item->product_id,
                productName: $item->product->name,
                quantity: $item->quantity,
                price: $item->price,
                subtotal: $item->getSubtotal(),
                variations: $item->variations,
            );
        })->toArray();

        $subtotal = $cartItems->sum(fn($item) => $item->getSubtotal());
        $totalItems = $cartItems->sum('quantity');
        $shippingCost = $this->shippingService->calculateShipping($subtotal);
        $total = $subtotal + $shippingCost;
        $shippingDescription = $this->shippingService->getShippingDescription($subtotal);

        return new CartDTO(
            items: $items,
            subtotal: $subtotal,
            totalItems: $totalItems,
            shippingCost: $shippingCost,
            total: $total,
            shippingDescription: $shippingDescription,
        );
    }

    public function clearCart(): void
    {
        $sessionId = $this->sessionService->getCurrentId();
        
        CartItem::where('session_id', $sessionId)->delete();
    }

}