<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Support\Facades\Log;
use Illuminate\Console\Command;
use App\Models\Product;

// specify the command signature and description using attributes
#[Signature('products:check-low-stock {threshold=10}')]
#[Description('Check for products with low stock and log a warning!')]
class CheckLowStock extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get the threshold value from the command argument, default to 5 if not provided
        $threshold = (int) ($this->argument('threshold') ?? 5); // Default threshold is 5 if not provided
        $lowStockProducts = Product::where('stock', '<=', $threshold)
            ->where('is_active', true)
            ->get();

        $this->info("Found {$lowStockProducts->count()} low-stock product(s) (threshold: {$threshold}).");

        // Log a warning for each (collection of products) low-stock product
        foreach ($lowStockProducts as $product) {
            Log::warning('Low stock alert', [
                'product_id' => $product->id,
                'name' => $product->name,
                'stock' => $product->stock,
            ]);
        }
    }
}
