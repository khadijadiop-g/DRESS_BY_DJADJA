<?php

namespace App\Http\Controllers;

use App\Models\BackInStockSubscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RestockController extends Controller
{
    public function subscribe(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'contact_info' => 'required|string|max:100',
            'contact_type' => 'required|string|in:whatsapp,email',
        ]);

        BackInStockSubscription::updateOrCreate(
            [
                'product_variant_id' => $validated['variant_id'],
                'contact_info' => $validated['contact_info'],
            ],
            [
                'contact_type' => $validated['contact_type'],
                'status' => 'pending',
            ]
        );

        return back()->with('success', 'Vous serez alertée dès le retour en stock de votre taille !');
    }
}
