<?php

namespace App\Http\Controllers;

use App\Actions\Orders\CreateOrderAction;
use App\DTOs\CheckoutData;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CheckoutController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Checkout/Index');
    }

    public function store(Request $request, CreateOrderAction $action): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'required|string|max:30',
            'customer_email' => 'nullable|email|max:150',
            'delivery_zone' => 'required|string|in:dakar,region_sn,international_gp',
            'delivery_address' => 'required|string|max:500',
            'payment_method' => 'required|string|in:wave,orange_money,cod,stripe',
            'currency_used' => 'required|string|in:XOF,EUR',
            'items' => 'required|array|min:1',
            'items.*.variant_id' => 'required|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $checkoutData = CheckoutData::fromRequest($validated);
            $result = $action->execute($checkoutData);

            $order = $result['order'];

            return redirect()->route('checkout.success', ['orderNumber' => $order->order_number])
                ->with('success', 'Votre commande a été enregistrée avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function success(string $orderNumber): Response
    {
        $order = Order::with('items')->where('order_number', $orderNumber)->firstOrFail();

        return Inertia::render('Checkout/Success', [
            'order' => $order,
        ]);
    }
}
