<?php
namespace App\Jobs;

use App\Services\Contact\GoogleContactsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncGoogleContactJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected ?string $email;
    protected ?string $action;
    protected ?array $data;
    protected ?string $originalEmail;

    public function __construct(?string $email, string $action, ?array $data = [], ?string $originalEmail = null)
    {
        $this->email = $email;
        $this->action = $action;
        $this->data = $data;
        $this->originalEmail = $originalEmail;
    }

    public function handle()
    {
        if (empty($this->email) || empty($this->action)) {
            return;
        }

        try {
            $googleContacts = app(GoogleContactsService::class);

            match ($this->action) {
                'created', 'updated' => $googleContacts->updateOrCreate($this->originalEmail ?: $this->email, $this->data),
                'deleted' => $googleContacts->deleteContactByEmail($this->email),
            };

            info("Google Contacts: Email: {$this->email} was {$this->action}.", $this->data);
        } catch (\Throwable $e) {
            logger()->error("Failed to sync contact {$this->email} with Google Contacts during {$this->action}.", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
