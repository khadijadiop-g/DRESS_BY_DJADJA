<?php

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
});

test('visitor can view homepage, switch currency and view dress with custom size', function () {
    $homeResponse = $this->get('/');
    $homeResponse->assertStatus(200);

    $currencyResponse = $this->post('/currency/switch', ['currency' => 'EUR']);
    $currencyResponse->assertSessionHas('currency', 'EUR');

    $dress = Product::first();
    $showResponse = $this->get("/robes/{$dress->slug}");
    $showResponse->assertStatus(200);
});

test('client can login with phone number and access customer dashboard', function () {
    $response = $this->post('/login', [
        'login' => '+221771234567',
        'password' => 'Client2026!',
    ]);

    $response->assertRedirect('/mon-compte');
    $this->assertAuthenticated();

    $dashResponse = $this->get('/mon-compte');
    $dashResponse->assertStatus(200);
});

test('client can save custom measurements', function () {
    $client = User::where('role', User::ROLE_CLIENT)->first();

    $response = $this->actingAs($client)->post('/mon-compte/mensurations', [
        'poitrine' => 90,
        'taille' => 72,
        'hanches' => 98,
        'longueur' => 172,
    ]);

    $response->assertRedirect();
    $client->refresh();

    expect((int)$client->measurements['poitrine'])->toBe(90)
        ->and((int)$client->measurements['taille'])->toBe(72);
});

test('non-admin user is blocked from admin dashboard', function () {
    $client = User::where('role', User::ROLE_CLIENT)->first();

    $response = $this->actingAs($client)->get('/admin');
    $response->assertRedirect('/login');
});

test('super admin can login and access admin dashboard and crud robes', function () {
    $admin = User::where('role', User::ROLE_SUPER_ADMIN)->first();

    $dashResponse = $this->actingAs($admin)->get('/admin');
    $dashResponse->assertStatus(200);

    $productsResponse = $this->actingAs($admin)->get('/admin/products');
    $productsResponse->assertStatus(200);

    $category = Category::first();
    $createProductResponse = $this->actingAs($admin)->post('/admin/products', [
        'category_id' => $category->id,
        'name' => 'Robe Royale DjaDja',
        'subtitle' => 'Soie Lourde & Perles',
        'description' => 'Création majestueuse brodée de perles fines.',
        'fabric_details' => '100% Soie',
        'care_guide' => 'Nettoyage à sec',
        'base_price_xof' => 95000,
        'is_featured' => true,
        'is_active' => true,
        'is_new_arrival' => true,
        'images' => [
            ['url' => 'https://example.com/dress1.jpg', 'is_hover' => false],
            ['url' => 'https://example.com/dress2.jpg', 'is_hover' => true],
        ],
        'variants' => [
            ['size' => 'M', 'color_name' => 'Or', 'color_hex' => '#D4AF37', 'stock_quantity' => 4],
        ],
    ]);

    $createProductResponse->assertRedirect('/admin/products');
    $this->assertDatabaseHas('products', ['name' => 'Robe Royale DjaDja']);
});

test('super admin can create dress with duplicate variant colors without sku collision', function () {
    $admin = User::where('role', User::ROLE_SUPER_ADMIN)->first();
    $category = Category::first();

    $response = $this->actingAs($admin)->post('/admin/products', [
        'category_id' => $category->id,
        'name' => 'Marieniere Test',
        'description' => 'Test dress with duplicate color variants.',
        'base_price_xof' => 45000,
        'images' => [
            ['url' => 'https://example.com/photo.jpg', 'is_hover' => false],
        ],
        'variants' => [
            ['size' => 'S', 'color_name' => 'Noir', 'color_hex' => '#111111', 'stock_quantity' => 5],
            ['size' => 'S', 'color_name' => 'Noir', 'color_hex' => '#111111', 'stock_quantity' => 2], // identical size & color
        ],
    ]);

    $response->assertRedirect('/admin/products');
    $product = Product::where('name', 'Marieniere Test')->first();
    expect($product->variants)->toHaveCount(2)
        ->and($product->variants[0]->sku)->not->toBe($product->variants[1]->sku);
});

test('super admin can create dress with real uploaded photo files and multiple colors', function () {
    \Illuminate\Support\Facades\Storage::fake('public');
    $admin = User::where('role', User::ROLE_SUPER_ADMIN)->first();
    $category = Category::first();

    $photo1 = \Illuminate\Http\UploadedFile::fake()->image('robe_front.jpg', 800, 1200);
    $photo2 = \Illuminate\Http\UploadedFile::fake()->image('robe_back.jpg', 800, 1200);

    $response = $this->actingAs($admin)->post('/admin/products', [
        'category_id' => $category->id,
        'name' => 'Robe Multi-Couleurs Test',
        'subtitle' => 'Soie & Organza',
        'description' => 'Test dress with real uploaded files and multiple colors.',
        'base_price_xof' => 60000,
        'photos' => [$photo1, $photo2],
        'hover_photo_index' => 1,
        'variants' => [
            ['size' => 'S', 'color_name' => 'Noir Ébène', 'color_hex' => '#111111', 'stock_quantity' => 5],
            ['size' => 'M', 'color_name' => 'Noir Ébène', 'color_hex' => '#111111', 'stock_quantity' => 5],
            ['size' => 'S', 'color_name' => 'Blanc Ivoire', 'color_hex' => '#F8F6F0', 'stock_quantity' => 4],
            ['size' => 'M', 'color_name' => 'Blanc Ivoire', 'color_hex' => '#F8F6F0', 'stock_quantity' => 4],
            ['size' => 'L', 'color_name' => 'Vert Émeraude', 'color_hex' => '#1B4D3E', 'stock_quantity' => 3],
        ],
    ]);

    $response->assertRedirect('/admin/products');
    $product = Product::where('name', 'Robe Multi-Couleurs Test')->first();
    expect($product)->not->toBeNull();
    expect($product->variants)->toHaveCount(5);
    expect($product->images)->toHaveCount(2);

    $colors = $product->variants->pluck('color_name')->unique();
    expect($colors)->toHaveCount(3);
});
