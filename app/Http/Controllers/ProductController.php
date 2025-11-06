<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class ProductController extends Controller
{
    public function index()
    {
        $categories = Category::with(['products' => function($query) {
            $query->where('is_active', true);
        }])
        ->orderBy('sort_order')
        ->get();

        return response()->json($categories);
    }
}
