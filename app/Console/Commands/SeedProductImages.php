<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class SeedProductImages extends Command
{
    protected $signature   = 'products:seed-images';
    protected $description = 'Assign image paths to existing products';

    public function handle(): int
    {
        $map = [
            'Classic Milk Tea'    => 'products/classic_milk_tea.png',
            'Taro Milk Tea'       => 'products/taro_milk_tea.png',
            'Matcha Latte'        => 'products/matcha_latte.png',
            'Wintermelon Tea'     => 'products/wintermelon_tea.png',
            'Cheese Foam Milk Tea'=> 'products/cheese_foam_milk_tea.png',
            'French Fries'        => 'products/french_fries.png',
            'Chicken Popcorn'     => 'products/chicken_popcorn.png',
            'Mozzarella Sticks'   => 'products/mozzarella_sticks.png',
            'Tater Tots'          => 'products/tater_tots.png',
            'Onigiri'             => 'products/onigiri.png',
        ];

        foreach ($map as $name => $path) {
            $count = Product::where('name', $name)->update(['image_path' => $path]);
            $this->line("  Updated: {$name} → {$path} ({$count} rows)");
        }

        $this->info('All product images seeded successfully!');
        return self::SUCCESS;
    }
}
