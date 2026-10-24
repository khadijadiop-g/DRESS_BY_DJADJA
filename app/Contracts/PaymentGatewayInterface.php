<?php

namespace App\Contracts;

use App\DTOs\PaymentResult;
use App\Models\Order;

interface PaymentGatewayInterface
{
    /**
     * Get unique identifier for the payment method
     */
    public function getMethod(): string;

    /**
     * Process payment for an order
     */
    public function process(Order $order): PaymentResult;
}
