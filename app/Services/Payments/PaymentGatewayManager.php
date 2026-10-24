<?php

namespace App\Services\Payments;

use App\Contracts\PaymentGatewayInterface;
use InvalidArgumentException;

class PaymentGatewayManager
{
    /** @var array<string, PaymentGatewayInterface> */
    protected array $gateways = [];

    public function __construct()
    {
        $this->register(new CashOnDeliveryPaymentGateway());
        $this->register(new WavePaymentGateway());
        $this->register(new OrangeMoneyPaymentGateway());
        $this->register(new StripePaymentGateway());
    }

    public function register(PaymentGatewayInterface $gateway): void
    {
        $this->gateways[$gateway->getMethod()] = $gateway;
    }

    public function get(string $method): PaymentGatewayInterface
    {
        if (!isset($this->gateways[$method])) {
            throw new InvalidArgumentException("Passerelle de paiement non supportée : {$method}");
        }

        return $this->gateways[$method];
    }
}
