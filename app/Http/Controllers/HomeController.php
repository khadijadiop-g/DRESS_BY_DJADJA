<?php

namespace App\Http\Controllers;

use App\Contracts\ProductRepositoryInterface;
use App\Models\Category;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __construct(
        protected ProductRepositoryInterface $productRepository
    ) {}

    public function index(): Response
    {
        $featuredDresses = $this->productRepository->getFeaturedProducts(6);
        $newArrivals = $this->productRepository->getNewArrivals(6);
        $collections = Category::active()->withCount('products')->get();

        return Inertia::render('Home', [
            'featuredDresses' => $featuredDresses,
            'newArrivals' => $newArrivals,
            'collections' => $collections,
        ]);
    }
}
