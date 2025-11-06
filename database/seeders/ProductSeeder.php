<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $eintritte = Category::where('name', 'Eintritte')->first();

        Product::create([
            'category_id' => $eintritte->id,
            'name' => 'Erwachsene',
            'price' => 16.00,
            'is_active' => true
        ]);

        Product::create([
            'category_id' => $eintritte->id,
            'name' => 'Kinder bis 16 Jahre',
            'price' => 14.00,
            'is_active' => true
        ]);
    }
}
