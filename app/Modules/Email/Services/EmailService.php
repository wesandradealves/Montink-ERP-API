<?php

namespace App\Modules\Email\Services;

use App\Common\Traits\MoneyFormatter;
use App\Common\Traits\DocumentFormatter;
use App\Common\Enums\ResponseMessage;
use App\Modules\Email\DTOs\OrderConfirmationEmailDTO;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailService
{
    use MoneyFormatter, DocumentFormatter;
    
    private EmailTemplateService $templateService;
    
    public function __construct(EmailTemplateService $templateService)
    {
        $this->templateService = $templateService;
    }

    public function sendOrderConfirmationEmail(OrderConfirmationEmailDTO $dto): bool
    {
        $data = $this->prepareEmailData($dto);
        
        return $this->templateService->send(
            'order_confirmation',
            $dto->customerEmail,
            $data,
            $dto->customerName
        );
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