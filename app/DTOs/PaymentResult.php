<?php

namespace App\DTOs;

readonly class PaymentResult
{
    public function __construct(
        public bool $success,
        public string $status, // 'paid', 'pending', 'failed'
        public ?string $transactionId = null,
        public ?string $redirectUrl = null,
        public ?string $message = null
    ) {}

    public static function successful(string $transactionId, ?string $redirectUrl = null, ?string $message = null): self
    {
        return new self(
            success: true,
            status: 'paid',
            transactionId: $transactionId,
            redirectUrl: $redirectUrl,
            message: $message ?? 'Paiement effectué avec succès.'
        );
    }

    public static function pending(?string $redirectUrl = null, ?string $message = null): self
    {
        return new self(
            success: true,
            status: 'pending',
            redirectUrl: $redirectUrl,
            message: $message ?? 'Paiement en attente de validation.'
        );
    }

    public static function failed(string $message): self
    {
        return new self(
            success: false,
            status: 'failed',
            message: $message
        );
    }
}
