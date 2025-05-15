<?php
namespace App\Jobs;

use App\Services\Validation\PhoneValidationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ValidatePhoneNumberJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $phone;
    protected int $phoneId;

    //run example ValidatePhoneNumberJob::dispatch($phone->id, $phone->number);
    public function __construct(int $phoneId, string $phone)
    {
        $this->phoneId = $phoneId;
        $this->phone = $phone;
    }

    public function handle(PhoneValidationService $service): void
    {
        $result = $service->validate($this->phone);

        if ($result) {
            DB::table('phone_numbers')
                ->where('id', $this->phoneId)
                ->update([
                    'is_valid' => $result['valid'],
                    'carrier' => $result['carrier'],
                    'type' => $result['type'],
                    'validated_at' => now(),
                ]);
        }
    }
}
