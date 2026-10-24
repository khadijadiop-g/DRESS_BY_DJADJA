<?php

namespace App\Services\Payments;

use App\Contracts\PaymentGatewayInterface;
use App\DTOs\PaymentResult;
use App\Models\Order;

class StripePaymentGateway implements PaymentGatewayInterface
{
    public function getMethod(): string
    {
        return 'stripe';
    }

    public function process(Order $order): PaymentResult
    {
        $order->update([
            'payment_status' => 'pending',
            'order_status' => 'processing',
        ]);

        return PaymentResult::pending(
            message: 'Paiement par carte bancaire sécurisé initié.'
        );
    }
}
