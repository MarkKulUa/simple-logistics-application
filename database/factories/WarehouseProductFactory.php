<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;;
use App\Models\Warehouse;
use App\Models\Product;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class WarehouseProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'warehouse_id' => Warehouse::inRandomOrder()->first()->id ?? 1,
            'product_id' => Product::inRandomOrder()->first()->id ?? 1,
            'quantity_in_stock' => fake()->randomDigit(),
        ];
    }
}
