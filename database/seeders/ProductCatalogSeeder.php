<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductCatalogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 10 categories
        Category::factory()->count(5)->create();

        // Create 5-15 products for each category
        Category::all()->each(function ($category) {
            Product::factory()
            ->count(rand(5, 15))
            ->for($category)
            ->create();
        });
    }
}
