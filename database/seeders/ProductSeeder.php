<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Classic Milk Tea',
                'description' => 'Traditional black tea with creamy milk and brown sugar pearls.',
                'price' => 4.50,
                'category' => 'milktea',
            ],
            [
                'name' => 'Taro Milk Tea',
                'description' => 'Fragrant taro root blended with milk and chewy pearls.',
                'price' => 5.00,
                'category' => 'milktea',
            ],
            [
                'name' => 'Matcha Latte',
                'description' => 'Premium Japanese matcha green tea with steamed milk.',
                'price' => 5.50,
                'category' => 'milktea',
            ],
            [
                'name' => 'Wintermelon Tea',
                'description' => 'Refreshing wintermelon flavored tea with a touch of cream.',
                'price' => 4.50,
                'category' => 'milktea',
            ],
            [
                'name' => 'Cheese Foam Milk Tea',
                'description' => 'Our signature milk tea topped with a salty-sweet cheese foam.',
                'price' => 5.75,
                'category' => 'milktea',
            ],
            [
                'name' => 'French Fries',
                'description' => 'Crispy golden fries with a pinch of sea salt.',
                'price' => 3.50,
                'category' => 'snack',
            ],
            [
                'name' => 'Chicken Popcorn',
                'description' => 'Bite-sized seasoned chicken breast, fried to perfection.',
                'price' => 6.00,
                'category' => 'snack',
            ],
            [
                'name' => 'Mozzarella Sticks',
                'description' => 'Melt-in-your-mouth mozzarella sticks with marinara dip.',
                'price' => 5.50,
                'category' => 'snack',
            ],
            [
                'name' => 'Tater Tots',
                'description' => 'Crunchy potato nuggets served with spicy mayo.',
                'price' => 4.00,
                'category' => 'snack',
            ],
            [
                'name' => 'Onigiri',
                'description' => 'Japanese rice ball with a variety of savory fillings.',
                'price' => 3.00,
                'category' => 'snack',
            ],
        ];

        foreach ($products as $product) {
            \App\Models\Product::create($product);
        }
    }
}
