<?php

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
});

test('home page returns 200 with dresses and collections', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('Home')
        ->has('featuredDresses')
        ->has('collections')
    );
});

test('catalog page returns 200 with all dresses', function () {
    $response = $this->get('/collections');

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('Shop/Index')
        ->has('dresses')
    );
});

test('dress detail page returns 200 with dress details and variants', function () {
    $dress = Product::first();

    $response = $this->get("/robes/{$dress->slug}");

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('Shop/Show')
        ->where('dress.id', $dress->id)
    );
});

test('currency switch sets currency in session', function () {
    $response = $this->post('/currency/switch', [
        'currency' => 'EUR',
    ]);

    $response->assertSessionHas('currency', 'EUR');
});

test('checkout stores order and decrements variant stock', function () {
    $variant = ProductVariant::where('stock_quantity', '>', 2)->first();
    $initialStock = $variant->stock_quantity;

    $payload = [
        'customer_name' => 'Fatou Ndiaye',
        'customer_phone' => '+221771234567',
        'customer_email' => 'fatou@example.com',
        'delivery_zone' => 'dakar',
        'delivery_address' => 'Mermoz, Rue 3 Villa 45, Dakar',
        'payment_method' => 'cod',
        'currency_used' => 'XOF',
        'items' => [
            [
                'variant_id' => $variant->id,
                'quantity' => 1,
            ]
        ],
        'notes' => 'Appeler avant livraison.',
    ];

    $response = $this->post('/commander', $payload);

    $response->assertRedirect();
    $variant->refresh();
    expect($variant->stock_quantity)->toBe($initialStock - 1);

    $this->assertDatabaseHas('orders', [
        'customer_name' => 'Fatou Ndiaye',
        'customer_phone' => '+221771234567',
        'delivery_zone' => 'dakar',
    ]);
});
