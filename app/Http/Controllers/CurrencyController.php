<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function switch(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'currency' => 'required|string|in:XOF,EUR',
        ]);

        session(['currency' => $validated['currency']]);

        return back();
    }
}
