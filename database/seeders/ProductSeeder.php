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
        $gutscheine = Category::where('name', 'Gutscheine')->first();

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

        Product::create([
            'category_id' => $eintritte->id,
            'name' => '3D Filme',
            'price' => 18.00,
            'is_active' => true
        ]);

        Product::create([
            'category_id' => $eintritte->id,
            'name' => 'Cinebambi',
            'price' => 8.00,
            'is_active' => true
        ]);

        Product::create([
            'category_id' => $eintritte->id,
            'name' => 'Cinetreff',
            'price' => 10.00,
            'is_active' => true
        ]);

        Product::create([
            'category_id' => $eintritte->id,
            'name' => 'Cinetreff Senioren',
            'price' => 5.00,
            'is_active' => true
        ]);

        Product::create([
            'category_id' => $eintritte->id,
            'name' => 'Kinomontag',
            'price' => 12.00,
            'is_active' => true
        ]);

        Product::create([
            'category_id' => $eintritte->id,
            'name' => 'Allianz Tag des Kinos',
            'price' => 7.00,
            'is_active' => true
        ]);

        // Gutscheine
        Product::create([
            'category_id' => $gutscheine->id,
            'name' => 'Procinema',
            'price' => -16.00,
            'is_active' => true
        ]);

        Product::create([
            'category_id' => $gutscheine->id,
            'name' => 'Cinema4me',
            'price' => -16.00,
            'is_active' => true
        ]);

        Product::create([
            'category_id' => $gutscheine->id,
            'name' => 'Kleines Geschenk',
            'price' => 0.00,
            'is_active' => true
        ]);

        Product::create([
            'category_id' => $gutscheine->id,
            'name' => 'Cinema4me 2für1',
            'price' => -16.00,
            'is_active' => true
        ]);
    }
}
