<?php

namespace App\Services\Payments;

use App\Contracts\PaymentGatewayInterface;
use App\DTOs\PaymentResult;
use App\Models\Order;

class WavePaymentGateway implements PaymentGatewayInterface
{
    public function getMethod(): string
    {
        return 'wave';
    }

    public function process(Order $order): PaymentResult
    {
        // For MVP, set pending with instructions or Wave deep link
        $order->update([
            'payment_status' => 'pending',
            'order_status' => 'processing',
        ]);

        return PaymentResult::pending(
            message: 'Veuillez confirmer votre paiement via l\'application Wave.'
        );
    }
}
