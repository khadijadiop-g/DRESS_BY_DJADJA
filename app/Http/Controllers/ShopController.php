<?php

namespace App\Http\Controllers;

use App\Contracts\ProductRepositoryInterface;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ShopController extends Controller
{
    public function __construct(
        protected ProductRepositoryInterface $productRepository
    ) {}

    public function index(Request $request): Response
    {
        $filters = [
            'category_slug' => $request->query('collection'),
            'sort' => $request->query('sort', 'newest'),
        ];

        $dresses = $this->productRepository->getCatalog($filters);
        $collections = Category::active()->get();
        $activeCollection = !empty($filters['category_slug'])
            ? Category::where('slug', $filters['category_slug'])->first()
            : null;

        return Inertia::render('Shop/Index', [
            'dresses' => $dresses,
            'collections' => $collections,
            'activeCollection' => $activeCollection,
            'currentSort' => $filters['sort'],
        ]);
    }

    public function show(string $slug): Response
    {
        $dress = $this->productRepository->getBySlug($slug);

        if (!$dress) {
            abort(404, 'Robe introuvable.');
        }

        $relatedDresses = Product::with(['images', 'variants'])
            ->active()
            ->where('category_id', $dress->category_id)
            ->where('id', '!=', $dress->id)
            ->take(4)
            ->get();

        return Inertia::render('Shop/Show', [
            'dress' => $dress,
            'relatedDresses' => $relatedDresses,
        ]);
    }
}
