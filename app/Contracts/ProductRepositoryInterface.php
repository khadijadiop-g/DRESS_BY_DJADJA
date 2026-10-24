<?php

namespace App\Contracts;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryInterface
{
    public function getFeaturedProducts(int $limit = 8): Collection;
    public function getNewArrivals(int $limit = 8): Collection;
    public function getBySlug(string $slug): ?Product;
    public function getCatalog(array $filters = []): Collection;
}
