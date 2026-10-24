<?php

namespace App\Http\Middleware;

use App\Models\Category;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $categories = Cache::remember('shared_categories_menu', 3600, function () {
            return Category::active()->select('id', 'name', 'slug')->get();
        });

        return [
            ...parent::share($request),
            'appName' => config('app.name', 'Dress By DjaDja'),
            'currency' => session('currency', 'XOF'),
            'currencyRate' => (float) Cache::remember('currency_rate_eur_xof', 86400, fn() => Setting::get('currency_rate_eur_xof', '655.957')),
            'whatsappNumber' => env('WHATSAPP_NUMBER') ?: Setting::get('whatsapp_number', '+221770000000'),
            'categoriesMenu' => $categories,
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'auth' => [
                'user' => $request->user(),
            ],
        ];
    }
}
