<?php

namespace App\Services\Payments;

use App\Contracts\PaymentGatewayInterface;
use App\DTOs\PaymentResult;
use App\Models\Order;

class CashOnDeliveryPaymentGateway implements PaymentGatewayInterface
{
    public function getMethod(): string
    {
        return 'cod';
    }

    public function process(Order $order): PaymentResult
    {
        $order->update([
            'payment_status' => 'pending',
            'order_status' => 'processing',
        ]);

        return PaymentResult::pending(
            message: 'Commande validée avec paiement à la livraison.'
        );
    }
}
