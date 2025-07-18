<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmação de Pedido #{{ $orderNumber }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #007bff;
            margin: 0;
        }
        .order-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .order-info p {
            margin: 5px 0;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .items-table th, .items-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .items-table th {
            background-color: #007bff;
            color: white;
        }
        .totals {
            margin-top: 20px;
            text-align: right;
        }
        .totals p {
            margin: 5px 0;
        }
        .total-final {
            font-size: 1.2em;
            font-weight: bold;
            color: #007bff;
            border-top: 2px solid #007bff;
            padding-top: 10px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 0.9em;
        }
        .address-section {
            background-color: #e9ecef;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Pedido Confirmado!</h1>
            <p>Obrigado por sua compra</p>
        </div>

        <div class="order-info">
            <p><strong>Número do Pedido:</strong> #{{ $orderNumber }}</p>
            <p><strong>Data:</strong> {{ $createdAt }}</p>
            <p><strong>Cliente:</strong> {{ $customerName }}</p>
            <p><strong>E-mail:</strong> {{ $customerEmail }}</p>
            <p><strong>Telefone:</strong> {{ $customerPhone }}</p>
            <p><strong>CPF:</strong> {{ $customerCpf }}</p>
        </div>

        <div class="address-section">
            <h3>Endereço de Entrega</h3>
            <p>{{ $deliveryAddress }}</p>
        </div>

        <h3>Itens do Pedido</h3>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Quantidade</th>
                    <th>Preço Unit.</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                <tr>
                    <td>
                        {{ $item['productName'] }}
                        @if(!empty($item['variations']))
                            <br><small>{{ json_encode($item['variations']) }}</small>
                        @endif
                    </td>
                    <td>{{ $item['quantity'] }}</td>
                    <td>R$ {{ number_format($item['price'], 2, ',', '.') }}</td>
                    <td>R$ {{ number_format($item['subtotal'], 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <p><strong>Subtotal:</strong> {{ $subtotal }}</p>
            
            @if($discount)
                <p><strong>Desconto</strong>
                    @if($couponCode)
                        (Cupom: {{ $couponCode }}):
                    @else
                        :
                    @endif
                    -{{ $discount }}
                </p>
            @endif
            
            <p><strong>Frete:</strong> {{ $shippingCost }}</p>
            <p class="total-final"><strong>Total:</strong> {{ $total }}</p>
        </div>

        <div class="footer">
            <p>Este é um e-mail automático. Por favor, não responda.</p>
            <p>Em caso de dúvidas, entre em contato conosco.</p>
        </div>
    </div>
</body>
</html>