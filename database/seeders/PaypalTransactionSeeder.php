<?php

namespace Database\Seeders;

use App\Models\Payment\PaypalTransaction;
use Illuminate\Database\Seeder;

class PaypalTransactionSeeder extends Seeder
{
    public function run(): void
    {
        PaypalTransaction::factory()->count(5)->create();
    }
}
