<?php

use App\Http\Controllers\Admin\AdminCollectionController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RestockController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Vitrine Publique (Visiteur / Client)
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/collections', [ShopController::class, 'index'])->name('shop.index');
Route::get('/robes/{slug}', [ShopController::class, 'show'])->name('shop.show');

Route::post('/currency/switch', [CurrencyController::class, 'switch'])->name('currency.switch');
Route::post('/restock/subscribe', [RestockController::class, 'subscribe'])->name('restock.subscribe');

Route::get('/commander', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/commander', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/commande/confirmation/{orderNumber}', [CheckoutController::class, 'success'])->name('checkout.success');

/*
|--------------------------------------------------------------------------
| Authentification (Email ou Téléphone)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Espace Client Connecté
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('mon-compte')->as('customer.')->group(function () {
    Route::get('/', [CustomerController::class, 'dashboard'])->name('dashboard');
    Route::post('/mensurations', [CustomerController::class, 'updateMeasurements'])->name('measurements.update');
    Route::post('/profil', [CustomerController::class, 'updateProfile'])->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| Espace SuperAdmin (DjaDja)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'super_admin'])->prefix('admin')->as('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // CRUD Robes
    Route::resource('products', AdminProductController::class)->except(['show']);

    // CRUD Collections
    Route::resource('collections', AdminCollectionController::class)->except(['create', 'show', 'edit']);

    // Gestion des Commandes & WhatsApp 1-clic
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
});
