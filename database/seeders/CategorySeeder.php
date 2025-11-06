<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create([
            'name' => 'Eintritte',
            'sort_order' => 1,
        ]);

        Category::create([
            'name' => 'Kiosk',
            'sort_order' => 2,
        ]);
    }
}
