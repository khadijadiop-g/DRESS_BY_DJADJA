<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class CustomerController extends Controller
{
    public function dashboard(): Response
    {
        $user = Auth::user();
        
        $orders = Order::with('items')
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere('customer_email', $user->email)
                    ->orWhere('customer_phone', $user->phone);
            })
            ->latest()
            ->get();

        return Inertia::render('Customer/Dashboard', [
            'user' => $user,
            'orders' => $orders,
        ]);
    }

    public function updateMeasurements(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'poitrine' => 'nullable|numeric|min:50|max:200',
            'taille' => 'nullable|numeric|min:40|max:180',
            'hanches' => 'nullable|numeric|min:50|max:220',
            'longueur' => 'nullable|numeric|min:80|max:220',
        ]);

        $user = Auth::user();
        $user->update([
            'measurements' => $validated,
        ]);

        return back()->with('success', 'Vos mensurations ont été enregistrées avec succès.');
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:30|unique:users,phone,' . $user->id,
            'address' => 'nullable|string|max:500',
        ]);

        $user->update($validated);

        return back()->with('success', 'Vos informations ont été mises à jour.');
    }
}
