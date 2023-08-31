<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        User::where('email', 'test@example.com')->first()
            ? User::factory(1)->create()
            : User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        \App\Models\Supplier::factory(2)->create();
        \App\Models\Warehouse::factory(2)->create();
        \App\Models\Product::factory(3)->create();
        \App\Models\WarehouseProduct::factory(2)->create();
    }
}
