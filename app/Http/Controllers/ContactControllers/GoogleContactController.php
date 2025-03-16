<?php

namespace App\Http\Controllers\ContactControllers;

use App\Models\Contact;
use App\Http\Controllers\Controller;
use App\Models\Partner;
use App\Services\Contact\GoogleContactsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class GoogleContactController extends Controller
{
    protected GoogleContactsService $googleContactsService;

    public function __construct()
    {
        if (request()->path() === 'export-for-google-contacts') {
            return;
        }

        parent::__construct();
        $this->googleContactsService = new GoogleContactsService();
    }

    /**
     * Get and write to the log a list of all contacts.
     *
     * @return ?array
     */
    public function list(): ?array
    {
        $contacts = [];

        try {
            $contacts = $this->googleContactsService->getFormattedContacts();
        } catch (\Exception $e) {
            Log::error('Error while getting contacts: ' . $e->getMessage());
        }

        return $contacts;
    }

    /**
     * Find contact by email
     *
     * @param string $email
     * @return array
     */
    public function search(string $email): array
    {
        $contact = null;
        try {
            $contact = $this->googleContactsService->findContactByEmail($email);
            if ($contact) {
                Log::info('Contact information:', ['contact' => $contact]);
            } else {
                Log::info('Contact by email ' . $email . ' not found.');
            }
        } catch (\Exception $e) {
            Log::error('Error while searching for contact: ' . $e->getMessage());
        }

        return $contact ?? [];
    }

    /**
     * Update contact by email.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateContactByEmail(Request $request): JsonResponse
    {
        $email = $request->get('email');
        $data = $request->except('email');

        if (!$email) {
            return response()->json(['error' => 'Email is required'], 400);
        }

        try {
            $updatedContact = $this->googleContactsService->updateContactByEmail($email, $data);
            return response()->json($updatedContact);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    /**
     * Export all contacts and partners to a CSV file and provide an immediate download.
     * The file is deleted after download.
     *
     * @return BinaryFileResponse
     */
    public function exportToCsv(): BinaryFileResponse
    {
        $fileName = 'google_contacts_export_' . time() . '.csv';
        $filePath = storage_path("app/{$fileName}");

        $handle = fopen($filePath, 'w');
        fputcsv($handle, ['First Name', 'Last Name', 'Email']);

        // Export contacts
        Contact::with('tags')->whereNotNull('email')->chunk(100, function ($contacts) use ($handle) {
            foreach ($contacts as $contact) {
                $data = $contact->getGoogleSyncData();

                fputcsv($handle, $data);
            }
        });

        fclose($handle);

        return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
    }
}
