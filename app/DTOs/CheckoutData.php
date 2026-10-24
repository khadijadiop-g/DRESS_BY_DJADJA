<?php

namespace App\DTOs;

readonly class CheckoutData
{
    /**
     * @param array<int, array{variant_id: int, quantity: int}> $items
     */
    public function __construct(
        public string $customerName,
        public string $customerPhone,
        public ?string $customerEmail,
        public string $deliveryZone, // 'dakar', 'region_sn', 'international_gp'
        public string $deliveryAddress,
        public string $paymentMethod, // 'wave', 'orange_money', 'cod', 'stripe'
        public string $currencyUsed, // 'XOF' or 'EUR'
        public array $items,
        public ?string $notes = null
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            customerName: (string) $data['customer_name'],
            customerPhone: (string) $data['customer_phone'],
            customerEmail: !empty($data['customer_email']) ? (string) $data['customer_email'] : null,
            deliveryZone: (string) ($data['delivery_zone'] ?? 'dakar'),
            deliveryAddress: (string) $data['delivery_address'],
            paymentMethod: (string) ($data['payment_method'] ?? 'cod'),
            currencyUsed: (string) ($data['currency_used'] ?? 'XOF'),
            items: (array) $data['items'],
            notes: !empty($data['notes']) ? (string) $data['notes'] : null
        );
    }
}
