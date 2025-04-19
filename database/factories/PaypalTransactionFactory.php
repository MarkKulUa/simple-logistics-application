<?php

namespace Database\Factories;

use App\Models\Payment\PaypalTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<PaypalTransaction>
 */
class PaypalTransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'paypal_order_id' => strtoupper(Str::random(12)),
            'status' => 'CREATED',
            'amount' => $this->faker->randomFloat(2, 10, 500),
            'currency' => 'USD',
            'raw_payload' => [],
        ];
    }
}
