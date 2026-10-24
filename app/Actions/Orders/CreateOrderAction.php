<?php

namespace App\Actions\Orders;

use App\Contracts\CurrencyConverterInterface;
use App\DTOs\CheckoutData;
use App\DTOs\PaymentResult;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Services\Payments\PaymentGatewayManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class CreateOrderAction
{
    public function __construct(
        protected CurrencyConverterInterface $currencyConverter,
        protected PaymentGatewayManager $paymentManager
    ) {}

    public function execute(CheckoutData $data): array
    {
        return DB::transaction(function () use ($data) {
            $variantIds = array_column($data->items, 'variant_id');
            $variants = ProductVariant::with('product')
                ->whereIn('id', $variantIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $subtotalXof = 0;
            $orderItemsData = [];

            foreach ($data->items as $item) {
                $variant = $variants->get($item['variant_id']);

                if (!$variant) {
                    throw new InvalidArgumentException("Variante introuvable.");
                }

                if ($variant->stock_quantity < $item['quantity']) {
                    throw new InvalidArgumentException("Stock insuffisant pour {$variant->product->name} ({$variant->size}).");
                }

                $unitPriceXof = $variant->effective_price_xof;
                $lineSubtotal = $unitPriceXof * $item['quantity'];
                $subtotalXof += $lineSubtotal;

                $orderItemsData[] = [
                    'variant' => $variant,
                    'quantity' => $item['quantity'],
                    'unit_price_xof' => $unitPriceXof,
                    'subtotal_xof' => $lineSubtotal,
                ];
            }

            // Calculate shipping
            $shippingAmountXof = match ($data->deliveryZone) {
                'dakar' => ($subtotalXof >= 50000) ? 0 : 2500,
                'region_sn' => 4500,
                'international_gp' => 15000,
                default => 2500,
            };

            $totalAmountXof = $subtotalXof + $shippingAmountXof;
            $totalAmountEur = $this->currencyConverter->xofToEur($totalAmountXof);

            $orderNumber = 'DJADJA-' . date('Ymd') . '-' . strtoupper(Str::random(4));

            $order = Order::create([
                'order_number' => $orderNumber,
                'customer_name' => $data->customerName,
                'customer_phone' => $data->customerPhone,
                'customer_email' => $data->customerEmail,
                'delivery_zone' => $data->deliveryZone,
                'delivery_address' => $data->deliveryAddress,
                'currency_used' => $data->currencyUsed,
                'subtotal_xof' => $subtotalXof,
                'shipping_amount_xof' => $shippingAmountXof,
                'total_amount_xof' => $totalAmountXof,
                'total_amount_eur' => $totalAmountEur,
                'payment_method' => $data->paymentMethod,
                'payment_status' => 'pending',
                'order_status' => 'pending',
                'notes' => $data->notes,
            ]);

            foreach ($orderItemsData as $itemData) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_variant_id' => $itemData['variant']->id,
                    'product_name' => $itemData['variant']->product->name,
                    'size' => $itemData['variant']->size,
                    'color_name' => $itemData['variant']->color_name,
                    'unit_price_xof' => $itemData['unit_price_xof'],
                    'quantity' => $itemData['quantity'],
                    'subtotal_xof' => $itemData['subtotal_xof'],
                ]);

                // Decrement stock
                $itemData['variant']->decrement('stock_quantity', $itemData['quantity']);
            }

            // Process payment strategy
            $gateway = $this->paymentManager->get($data->paymentMethod);
            $paymentResult = $gateway->process($order);

            return [
                'order' => $order->fresh(['items']),
                'payment_result' => $paymentResult,
            ];
        });
    }
}
