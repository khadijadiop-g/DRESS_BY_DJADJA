<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class AdminCollectionController extends Controller
{
    public function index(): Response
    {
        $collections = Category::withCount('products')->orderBy('sort_order')->get();

        return Inertia::render('Admin/Collections/Index', [
            'collections' => $collections,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'image_url' => 'nullable|url',
            'is_active' => 'boolean',
        ]);

        Category::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'image_url' => $validated['image_url'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        Cache::flush();

        return back()->with('success', 'Collection créée avec succès.');
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'image_url' => 'nullable|url',
            'is_active' => 'boolean',
        ]);

        $category->update($validated);
        Cache::flush();

        return back()->with('success', 'Collection mise à jour.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();
        Cache::flush();

        return back()->with('success', 'Collection supprimée.');
    }
}
