<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Payment\PaypalTransaction;
use App\Services\Payment\PayPalService;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Command\Command as CommandAlias;

class SyncPaypalTransactions extends Command
{
    protected $signature = 'paypal:sync-transactions';
    protected $description = 'Syncs PayPal transaction statuses with PayPal API';

    public function handle(): int
    {
        $service = new PayPalService(
            config('services.paypal.client_id'),
            config('services.paypal.client_secret'),
            app('Illuminate\Http\Client\Factory'),
            config('services.paypal.sandbox')
        );

        $transactions = PaypalTransaction::where('status', '!=', 'COMPLETED')->get();

        if ($transactions->isEmpty()) {
            $this->info('No pending PayPal transactions found.');
            return CommandAlias::SUCCESS;
        }

        foreach ($transactions as $transaction) {
            try {
                $remote = $service->getTransactionDetails($transaction->paypal_order_id);

                if (!empty($remote['status']) && $remote['status'] !== $transaction->status) {
                    $transaction->update([
                        'status' => $remote['status'],
                        'raw_payload' => $remote,
                    ]);

                    $this->info("Updated transaction {$transaction->paypal_order_id} to [{$remote['status']}]");
                }
            } catch (\Throwable $e) {
                Log::error('Failed to sync PayPal transaction', [
                    'id' => $transaction->id,
                    'paypal_order_id' => $transaction->paypal_order_id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return CommandAlias::SUCCESS;
    }
}
