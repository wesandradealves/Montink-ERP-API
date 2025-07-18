<?php

namespace App\Modules\Email\Services;

use App\Common\Traits\MoneyFormatter;
use App\Common\Traits\DocumentFormatter;
use App\Modules\Email\DTOs\OrderConfirmationEmailDTO;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailService
{
    use MoneyFormatter, DocumentFormatter;

    public function sendOrderConfirmationEmail(OrderConfirmationEmailDTO $dto): bool
    {
        try {
            $data = $this->prepareEmailData($dto);
            
            Mail::send('emails.order-confirmation', $data, function ($message) use ($dto) {
                $message->to($dto->customerEmail, $dto->customerName)
                    ->subject('Confirmação de Pedido #' . $dto->orderNumber);
            });

            return true;
        } catch (\Exception $e) {
            Log::error('Erro ao enviar email de confirmação: ' . $e->getMessage(), [
                'order_number' => $dto->orderNumber,
                'customer_email' => $dto->customerEmail
            ]);
            return false;
        }
    }

    private function prepareEmailData(OrderConfirmationEmailDTO $dto): array
    {
        return [
            'orderNumber' => $dto->orderNumber,
            'customerName' => $dto->customerName,
            'customerEmail' => $dto->customerEmail,
            'customerPhone' => $dto->customerPhone,
            'customerCpf' => $dto->customerCpf,
            'deliveryAddress' => $this->formatDeliveryAddress($dto),
            'items' => $dto->items,
            'subtotal' => $this->formatMoney($dto->subtotal),
            'discount' => $dto->discount > 0 ? $this->formatMoney($dto->discount) : null,
            'couponCode' => $dto->couponCode,
            'shippingCost' => $this->formatMoney($dto->shippingCost),
            'total' => $this->formatMoney($dto->total),
            'createdAt' => $dto->createdAt->format('d/m/Y H:i')
        ];
    }

    private function formatDeliveryAddress(OrderConfirmationEmailDTO $dto): string
    {
        $address = $dto->customerAddress;
        
        if ($dto->customerComplement) {
            $address .= ', ' . $dto->customerComplement;
        }
        
        $address .= ' - ' . $dto->customerNeighborhood;
        $address .= ', ' . $dto->customerCity . '/' . $dto->customerState;
        $address .= ' - CEP: ' . $this->formatCep($dto->customerCep);
        
        return $address;
    }

}