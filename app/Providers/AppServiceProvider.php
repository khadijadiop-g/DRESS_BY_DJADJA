<?php

namespace App\Providers;

use App\Contracts\CurrencyConverterInterface;
use App\Contracts\ProductRepositoryInterface;
use App\Repositories\EloquentProductRepository;
use App\Services\Currency\CurrencyConverterService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CurrencyConverterInterface::class, CurrencyConverterService::class);
        $this->app->bind(ProductRepositoryInterface::class, EloquentProductRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
