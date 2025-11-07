<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $location = $request->query('location');

        $categories = Category::with(['products' => function($query) use ($location) {
            $query->where('is_active', true);

            // Für Kiosk-Produkte: nur standort-spezifische laden
            // Für andere Kategorien (Eintritte, Gutscheine): alle laden (location = null)
            $query->where(function($q) use ($location) {
                $q->whereNull('location')
                  ->orWhere('location', $location);
            });
        }])
        ->orderBy('sort_order')
        ->get();

        return response()->json($categories);
    }
}
