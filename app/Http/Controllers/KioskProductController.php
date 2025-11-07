<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class KioskProductController extends Controller
{
    /**
     * Get all kiosk products (global, not location-specific)
     */
    public function index(Request $request)
    {
        $kioskCategory = Category::where('name', 'Kiosk')->first();

        if (!$kioskCategory) {
            return response()->json(['products' => []]);
        }

        // Kiosk-Produkte sind jetzt global (location wird ignoriert)
        $products = Product::where('category_id', $kioskCategory->id)
            ->orderBy('name')
            ->get();

        return response()->json(['products' => $products]);
    }

    /**
     * Store a new kiosk product (global, not location-specific)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'barcode' => 'required|string|unique:products,barcode',
            'price' => 'required|numeric|min:0',
        ]);

        $kioskCategory = Category::where('name', 'Kiosk')->first();

        if (!$kioskCategory) {
            return response()->json(['error' => 'Kiosk category not found'], 404);
        }

        $product = Product::create([
            'category_id' => $kioskCategory->id,
            'name' => $validated['name'],
            'barcode' => $validated['barcode'],
            'price' => $validated['price'],
            'location' => null, 
            'is_active' => true,
        ]);

        return response()->json(['product' => $product], 201);
    }

    /**
     * Update a kiosk product
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'barcode' => 'sometimes|required|string|unique:products,barcode,' . $id,
            'price' => 'sometimes|required|numeric|min:0',
        ]);

        $product->update($validated);

        return response()->json(['product' => $product]);
    }

    /**
     * Delete a kiosk product
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }

    /**
     * Verify settings password
     */
    public function verifyPassword(Request $request)
    {
        $password = $request->input('password');

        if ($password === '270696') {
            return response()->json(['valid' => true]);
        }

        return response()->json(['valid' => false], 401);
    }
}
