<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\CurrencyConverterInterface;
use App\Http\Controllers\Controller;
use App\Models\BackInStockSubscription;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Setting;
use Inertia\Inertia;
use Inertia\Response;

class AdminDashboardController extends Controller
{
    public function __construct(
        protected CurrencyConverterInterface $currencyConverter
    ) {}

    public function index(): Response
    {
        $todayRevenueXof = Order::whereDate('created_at', today())
            ->where('payment_status', '!=', 'failed')
            ->sum('total_amount_xof');

        $monthRevenueXof = Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('payment_status', '!=', 'failed')
            ->sum('total_amount_xof');

        $pendingOrdersCount = Order::whereIn('order_status', ['pending', 'processing'])->count();
        $totalOrdersCount = Order::count();
        $totalProductsCount = Product::count();
        $lowStockVariantsCount = ProductVariant::where('stock_quantity', '<=', 1)->count();
        $pendingRestocksCount = BackInStockSubscription::where('status', 'pending')->count();

        $recentOrders = Order::with('items')
            ->latest()
            ->take(8)
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'metrics' => [
                'todayRevenueXof' => (int) $todayRevenueXof,
                'todayRevenueEur' => $this->currencyConverter->xofToEur($todayRevenueXof),
                'monthRevenueXof' => (int) $monthRevenueXof,
                'monthRevenueEur' => $this->currencyConverter->xofToEur($monthRevenueXof),
                'pendingOrders' => $pendingOrdersCount,
                'totalOrders' => $totalOrdersCount,
                'totalProducts' => $totalProductsCount,
                'lowStockVariants' => $lowStockVariantsCount,
                'pendingRestocks' => $pendingRestocksCount,
            ],
            'recentOrders' => $recentOrders,
        ]);
    }
}
