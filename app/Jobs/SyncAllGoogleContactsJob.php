<?php

namespace App\Jobs;

use App\Models\Partner;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncAllGoogleContactsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private int $chunkSize = 5;

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            $totalPartners = Partner::whereNotNull('email')->distinct('email')->count('email');
            $this->logAndEcho("Starting Google Contacts sync for {$totalPartners} unique partners.");

            // Sync partners
            Partner::with('cityBranches')->whereNotNull('email')->lazy($this->chunkSize)->each(function ($partner, $index) {
                if (!empty($partner->email)) {
                    SyncGoogleContactJob::dispatch($partner->email, 'updated', $partner->getGoogleSyncData());
                    $this->logAndEcho("Synced partner: {$partner->email}");
                }
                if (($index + 1) % $this->chunkSize === 0) {
                    $this->logAndEcho("Processed " . ($index + 1) . " partners, sleeping for 10 seconds.");
                    sleep(10);
                }
            });

            $this->logAndEcho('Completed Google Contacts sync for all partners.');
        } catch (\Throwable $e) {
            Log::error('Error in SyncAllGoogleContactsJob', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->logAndEcho("Error: " . $e->getMessage());
        }
    }

    /**
     * Logs the message and prints it to the console if running in CLI.
     */
    private function logAndEcho(string $message): void
    {
        info($message);
        if (app()->runningInConsole()) {
            echo "$message\n";
        }
    }
}
