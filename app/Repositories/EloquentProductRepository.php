<?php

namespace App\Repositories;

use App\Contracts\ProductRepositoryInterface;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class EloquentProductRepository implements ProductRepositoryInterface
{
    public function getFeaturedProducts(int $limit = 8): Collection
    {
        return Cache::remember("products_featured_{$limit}", 3600, function () use ($limit) {
            return Product::with(['category', 'images', 'variants'])
                ->active()
                ->featured()
                ->take($limit)
                ->get();
        });
    }

    public function getNewArrivals(int $limit = 8): Collection
    {
        return Cache::remember("products_new_{$limit}", 3600, function () use ($limit) {
            return Product::with(['category', 'images', 'variants'])
                ->active()
                ->newArrivals()
                ->latest()
                ->take($limit)
                ->get();
        });
    }

    public function getBySlug(string $slug): ?Product
    {
        return Cache::remember("product_slug_{$slug}", 3600, function () use ($slug) {
            return Product::with(['category', 'images', 'variants'])
                ->active()
                ->where('slug', $slug)
                ->first();
        });
    }

    public function getCatalog(array $filters = []): Collection
    {
        $query = Product::with(['category', 'images', 'variants'])->active();

        if (!empty($filters['category_slug'])) {
            $query->whereHas('category', function ($q) use ($filters) {
                $q->where('slug', $filters['category_slug']);
            });
        }

        if (!empty($filters['sort'])) {
            match ($filters['sort']) {
                'price_asc' => $query->orderBy('base_price_xof', 'asc'),
                'price_desc' => $query->orderBy('base_price_xof', 'desc'),
                'newest' => $query->latest(),
                default => $query->latest(),
            };
        } else {
            $query->latest();
        }

        return $query->get();
    }
}
