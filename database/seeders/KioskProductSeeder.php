<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class KioskProductSeeder extends Seeder
{
    public function run(): void
    {
        $kioskCategory = Category::where('name', 'Kiosk')->first();

        if (!$kioskCategory) {
            echo "Kiosk category not found!\n";
            return;
        }

        // Lösche alte Kiosk-Produkte
        Product::where('category_id', $kioskCategory->id)->delete();

        $products = [
            // Softgetränke
            ['name' => 'Softgetränk', 'price' => 4.50],

            // Biere
            ['name' => 'Corona', 'price' => 6.00],
            ['name' => 'Sommerspy', 'price' => 6.00],
            ['name' => 'Moretti', 'price' => 5.00],
            ['name' => 'Sagres', 'price' => 5.00],
            ['name' => 'Bügel Bier', 'price' => 6.00],

            // Popcorn
            ['name' => 'Popcorn M', 'price' => 4.00],
            ['name' => 'Popcorn M+Getränk', 'price' => 7.50],
            ['name' => 'Popcorn L', 'price' => 6.00],
            ['name' => 'Popcorn L+Getränk', 'price' => 9.50],
            ['name' => 'Popcorn XL', 'price' => 9.00],
            ['name' => 'Popcorn XL+2 Getränke', 'price' => 16.00],
            ['name' => 'Popcorn XXL', 'price' => 12.00],
            ['name' => 'Popcorn XXL+3 Getränke', 'price' => 21.50],

            // Eis Kugeln
            ['name' => '1 Eiskugel', 'price' => 3.80],
            ['name' => '2 Eiskugeln', 'price' => 7.50],

            // Süssigkeiten
            ['name' => 'Süssigkeitenbox', 'price' => 4.50],
            ['name' => 'Lekker', 'price' => 6.00],
            ['name' => 'Pringles', 'price' => 3.00],
            ['name' => 'Chupa Chups', 'price' => 1.00],
            ['name' => 'Riegel', 'price' => 2.50],
            ['name' => 'Maltesers Gross', 'price' => 7.00],
            ['name' => 'Maltesers Klein', 'price' => 4.00],
            ['name' => 'M&Ms Gross', 'price' => 7.00],
            ['name' => 'M&Ms Klein', 'price' => 4.00],
            ['name' => 'Ovorocks', 'price' => 5.00],

            // Eis Packungen
            ['name' => 'Magnum', 'price' => 4.00],
            ['name' => 'Ben & Jerry\'s', 'price' => 5.50],
            ['name' => 'Cornetto', 'price' => 4.00],
            ['name' => 'Solero', 'price' => 4.00],
            ['name' => 'Calipso/Pushup/XPop', 'price' => 4.00],
        ];

        foreach ($products as $product) {
            Product::create([
                'category_id' => $kioskCategory->id,
                'name' => $product['name'],
                'price' => $product['price'],
                'barcode' => null,
                'location' => null,
                'is_active' => true,
            ]);
        }

        echo "Kiosk products created successfully!\n";
    }
}

