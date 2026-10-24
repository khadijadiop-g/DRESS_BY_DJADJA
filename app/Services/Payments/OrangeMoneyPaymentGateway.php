<?php

namespace App\Services\Payments;

use App\Contracts\PaymentGatewayInterface;
use App\DTOs\PaymentResult;
use App\Models\Order;

class OrangeMoneyPaymentGateway implements PaymentGatewayInterface
{
    public function getMethod(): string
    {
        return 'orange_money';
    }

    public function process(Order $order): PaymentResult
    {
        $order->update([
            'payment_status' => 'pending',
            'order_status' => 'processing',
        ]);

        return PaymentResult::pending(
            message: 'Veuillez valider le paiement Orange Money avec votre code secret.'
        );
    }
}
