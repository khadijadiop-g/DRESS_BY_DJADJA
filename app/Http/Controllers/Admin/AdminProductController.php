<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class AdminProductController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Product::with(['category', 'images', 'variants'])->latest();

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->query('category'));
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::active()->select('id', 'name')->get();

        return Inertia::render('Admin/Products/Index', [
            'products' => $products,
            'categories' => $categories,
            'filters' => $request->only(['search', 'category']),
        ]);
    }

    public function create(): Response
    {
        $categories = Category::active()->select('id', 'name')->get();

        return Inertia::render('Admin/Products/Create', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:150',
            'subtitle' => 'nullable|string|max:150',
            'description' => 'required|string',
            'fabric_details' => 'nullable|string',
            'care_guide' => 'nullable|string',
            'base_price_xof' => 'required|integer|min:1000',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'is_new_arrival' => 'boolean',
            'photos' => 'nullable|array',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,webp,avif|max:12288',
            'hover_photo_index' => 'nullable|integer',
            'images' => 'nullable|array',
            'images.*.url' => 'nullable|string',
            'images.*.is_hover' => 'boolean',
            'variants' => 'required|array|min:1',
            'variants.*.size' => 'required|string',
            'variants.*.color_name' => 'required|string',
            'variants.*.color_hex' => 'required|string',
            'variants.*.stock_quantity' => 'required|integer|min:0',
        ]);

        $hasUploadedPhotos = $request->hasFile('photos') && count($request->file('photos')) > 0;
        $hasUrlImages = !empty($validated['images']) && count(array_filter($validated['images'], fn($img) => !empty($img['url'] ?? null))) > 0;

        if (!$hasUploadedPhotos && !$hasUrlImages) {
            return back()->withErrors(['photos' => 'Veuillez télécharger au moins une photo pour cette robe.']);
        }

        $product = DB::transaction(function () use ($request, $validated, $hasUploadedPhotos, $hasUrlImages) {
            // Unique slug generation
            $baseSlug = Str::slug($validated['name']);
            $slug = $baseSlug ?: 'robe-creation';
            $slugCounter = 1;
            while (Product::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $slugCounter;
                $slugCounter++;
            }

            $product = Product::create([
                'category_id' => $validated['category_id'],
                'name' => $validated['name'],
                'slug' => $slug,
                'subtitle' => $validated['subtitle'] ?? null,
                'description' => $validated['description'],
                'fabric_details' => $validated['fabric_details'] ?? null,
                'care_guide' => $validated['care_guide'] ?? null,
                'base_price_xof' => $validated['base_price_xof'],
                'is_featured' => $validated['is_featured'] ?? false,
                'is_active' => $validated['is_active'] ?? true,
                'is_new_arrival' => $validated['is_new_arrival'] ?? true,
            ]);

            $imgIndex = 0;
            $hoverIdx = (int) ($validated['hover_photo_index'] ?? 1);

            // Handle uploaded photo files
            if ($hasUploadedPhotos) {
                foreach ($request->file('photos') as $idx => $photoFile) {
                    if ($photoFile && $photoFile->isValid()) {
                        $path = $photoFile->store('products', 'public');
                        ProductImage::create([
                            'product_id' => $product->id,
                            'url' => '/storage/' . $path,
                            'alt_text' => $product->name,
                            'is_hover' => ($idx === $hoverIdx),
                            'sort_order' => $imgIndex++,
                        ]);
                    }
                }
            }

            // Handle URL images if provided
            if ($hasUrlImages) {
                foreach ($validated['images'] as $img) {
                    if (!empty($img['url'])) {
                        ProductImage::create([
                            'product_id' => $product->id,
                            'url' => $img['url'],
                            'alt_text' => $product->name,
                            'is_hover' => $img['is_hover'] ?? false,
                            'sort_order' => $imgIndex++,
                        ]);
                    }
                }
            }

            // Safe unique SKU generation
            $usedSkus = [];
            foreach ($validated['variants'] as $v) {
                $baseSku = strtoupper(Str::slug($product->name . '-' . $v['color_name'] . '-' . $v['size']));
                if (empty($baseSku)) {
                    $baseSku = 'DJADJA-VAR';
                }

                $sku = $baseSku;
                $skuCounter = 1;
                while (ProductVariant::where('sku', $sku)->exists() || in_array($sku, $usedSkus)) {
                    $sku = $baseSku . '-' . $skuCounter;
                    $skuCounter++;
                }

                $usedSkus[] = $sku;

                ProductVariant::create([
                    'product_id' => $product->id,
                    'size' => $v['size'],
                    'color_name' => $v['color_name'],
                    'color_hex' => $v['color_hex'],
                    'sku' => $sku,
                    'stock_quantity' => $v['stock_quantity'],
                ]);
            }

            return $product;
        });

        Cache::flush();

        return redirect()->route('admin.products.index')
            ->with('success', "La robe {$product->name} a été créée avec succès.");
    }

    public function edit(Product $product): Response
    {
        $product->load(['images', 'variants']);
        $categories = Category::active()->select('id', 'name')->get();

        return Inertia::render('Admin/Products/Edit', [
            'product' => $product,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:150',
            'subtitle' => 'nullable|string|max:150',
            'description' => 'required|string',
            'fabric_details' => 'nullable|string',
            'care_guide' => 'nullable|string',
            'base_price_xof' => 'required|integer|min:1000',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'is_new_arrival' => 'boolean',
            'photos' => 'nullable|array',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,webp,avif|max:12288',
        ]);

        $product->update([
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'subtitle' => $validated['subtitle'] ?? null,
            'description' => $validated['description'],
            'fabric_details' => $validated['fabric_details'] ?? null,
            'care_guide' => $validated['care_guide'] ?? null,
            'base_price_xof' => $validated['base_price_xof'],
            'is_featured' => $validated['is_featured'] ?? false,
            'is_active' => $validated['is_active'] ?? true,
            'is_new_arrival' => $validated['is_new_arrival'] ?? true,
        ]);

        if ($request->hasFile('photos')) {
            $lastOrder = $product->images()->max('sort_order') ?? 0;
            foreach ($request->file('photos') as $idx => $photoFile) {
                if ($photoFile && $photoFile->isValid()) {
                    $path = $photoFile->store('products', 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'url' => '/storage/' . $path,
                        'alt_text' => $product->name,
                        'is_hover' => false,
                        'sort_order' => $lastOrder + $idx + 1,
                    ]);
                }
            }
        }

        Cache::flush();

        return redirect()->route('admin.products.index')
            ->with('success', "La robe {$product->name} a été mise à jour.");
    }

    public function destroy(Product $product): RedirectResponse
    {
        $name = $product->name;
        $product->delete();
        Cache::flush();

        return redirect()->route('admin.products.index')
            ->with('success', "La robe {$name} a été retirée du catalogue.");
    }
}
