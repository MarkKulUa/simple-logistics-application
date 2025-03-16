<?php

namespace App\Services\Contact;

use Google\Client;
use Google\Service\Exception;
use Google\Service\PeopleService;
use Google\Service\PeopleService\Person;

class GoogleContactsService
{
    protected Client $client;
    protected PeopleService $peopleService;

    /**
     * @throws \Google\Exception
     */
    public function __construct()
    {
        $this->client = new Client();
        $this->client->setAuthConfig(storage_path(config('services.google.credentials_path')));
        $this->client->addScope(config('services.google.scopes'));
        $this->client->setAccessType('offline');

        $this->setAccessToken();

        $this->peopleService = new PeopleService($this->client);
    }

    private function setAccessToken(): void
    {
        $tokenPath = storage_path(config('services.google.token_path'));

        if (file_exists($tokenPath)) {
            $accessToken = json_decode(file_get_contents($tokenPath), true);

            try {
                $this->client->setAccessToken($accessToken);

                $expiresAt = $accessToken['created'] + $accessToken['expires_in'];
                if ($this->client->isAccessTokenExpired() || ($expiresAt - time() < 86400)) {
                    $this->refreshAccessToken();
                }
            } catch (\Google\Service\Exception $e) {
                if (str_contains($e->getMessage(), 'invalid_grant')) {
                    $this->refreshAccessToken();
                } else {
                    throw $e;
                }
            }
        }
    }

    private function refreshAccessToken(): void
    {
        $tokenPath = storage_path(config('services.google.token_path'));
        $newAccessToken = $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());

        if (isset($newAccessToken['error'])) {
            throw new \Exception('Unable to refresh access token: ' . $newAccessToken['error_description']);
        }

        $newAccessToken['created'] = time();
        file_put_contents($tokenPath, json_encode($newAccessToken));
        $this->client->setAccessToken($newAccessToken);
    }

    /**
     * Getting a list of contacts.
     *
     * @return array
     * @throws Exception
     */
    public function listContacts(): array
    {
        $connections = $this->peopleService->people_connections->listPeopleConnections('people/me', [
            'personFields' => 'names,emailAddresses',
            'sources' => 'READ_SOURCE_TYPE_CONTACT'
        ]);

        return $connections->getConnections() ?? [];
    }

    /**
     * Search for a contact by email.
     *
     * @param string $email
     * @return Person|null
     * @throws Exception
     */
    public function findContactByEmail(string $email): ?array
    {
        $contacts = $this->listContacts();

        foreach ($contacts as $contact) {
            foreach ($contact->getEmailAddresses() as $emailAddress) {
                if ($emailAddress->getValue() === $email) {
                    return $this->formatContact($contact);
                }
            }
        }

        return null;
    }

    /**
     * Adding a new contact if the email does not exist.
     *
     * @param string $givenName
     * @param string $familyName
     * @param string $email
     * @return Person
     * @throws Exception
     */
    public function addContact(string $givenName, string $familyName, string $email): Person
    {
        if ($this->findContactByEmail($email)) {
            throw new Exception('A contact with this email already exists.');
        }

        $contact = new Person();
        $contact->setNames([new PeopleService\Name(['givenName' => $givenName, 'familyName' => $familyName])]);
        $contact->setEmailAddresses([new PeopleService\EmailAddress(['value' => $email])]);

        return $this->peopleService->people->createContact($contact);
    }

    /**
     * Universal method updating the contact by `resourceName`
     *
     * @param string $resourceName
     * @param array $data
     * @return Person
     * @throws Exception
     */
    public function updateContact(string $resourceName, array $data): Person
    {
        $contact = $this->peopleService->people->get($resourceName, [
            'personFields' => 'names,emailAddresses',
        ]);

        if (isset($data['givenName']) || isset($data['familyName'])) {
            $contact->setNames([new PeopleService\Name([
                'givenName' => $data['givenName'] ?? $contact->getNames()[0]?->getGivenName(),
                'familyName' => $data['familyName'] ?? $contact->getNames()[0]?->getFamilyName(),
            ])]);
        }

        if (isset($data['email'])) {
            $contact->setEmailAddresses([new PeopleService\EmailAddress(['value' => $data['email']])]);
        }

        $updateFieldsMap = [
            'givenName' => 'names',
            'familyName' => 'names',
            'email' => 'emailAddresses',
        ];

        $updatePersonFields = array_unique(array_values(array_intersect_key($updateFieldsMap, $data)));

        return $this->peopleService->people->updateContact($resourceName, $contact, [
            'updatePersonFields' => implode(',', $updatePersonFields),
            'personFields' => 'names,emailAddresses',
        ]);
    }

    /**
     * Updating the contact by email.
     *
     * @param string $email
     * @param array $data
     * @return Person|null
     * @throws Exception
     */
    public function updateContactByEmail(string $email, array $data): ?Person
    {
        $contact = $this->findContactByEmail($email);

        if (!$contact) {
            throw new Exception("Contact with email $email not found.");
        }

        return $this->updateContact($contact['resource_name'], $data);
    }

    /**
     * Delete contact.
     *
     * @param string $email
     * @return void
     * @throws Exception
     */
    public function deleteContactByEmail(string $email): void
    {
        $contact = $this->findContactByEmail($email);

        if (!$contact || !isset($contact['resource_name'])) {
            throw new Exception("Contact with email $email not found.");
        }

        $this->deleteContact($contact['resource_name']);
    }

    /**
     * Delete contact.
     *
     * @param string $resourceName
     * @return void
     * @throws Exception
     */
    public function deleteContact(string $resourceName): void
    {
        $this->peopleService->people->deleteContact($resourceName);
    }

    /**
     * Getting all contacts formatted.
     *
     * @return array
     * @throws Exception
     */
    public function getFormattedContacts(): array
    {
        $contacts = $this->listContacts();

        return array_map(fn ($contact) => $this->formatContact($contact), $contacts);
    }

    /**
     * Formatting one contact
     *
     * @param Person $contact
     * @return array
     */
    private function formatContact(Person $contact): array
    {
        $names = $contact->getNames() ?? [];
        $primaryName = collect($names)->firstWhere(fn($n) => $n->getMetadata()->getPrimary()) ?? $names[0] ?? null;

        $emails = $contact->getEmailAddresses() ?? [];
        $primaryEmail = collect($emails)->firstWhere(fn($e) => $e->getMetadata()->getPrimary()) ?? $emails[0] ?? null;

        $phoneNumbers = $contact->getPhoneNumbers() ?? [];
        $primaryPhone = collect($phoneNumbers)->firstWhere(fn($p) => $p->getMetadata()->getPrimary()) ?? $phoneNumbers[0] ?? null;

        return [
            'resource_name' => $contact?->getResourceName() ?? null,
            'etag' => $contact->getEtag() ?? null,
            'first_name' => $primaryName?->getGivenName() ?? null,
            'last_name' => $primaryName?->getFamilyName() ?? null,
            'full_name' => $primaryName?->getDisplayName() ?? null,
            'email' => $primaryEmail?->getValue() ?? null,
            'additional_emails' => array_map(fn($e) => $e->getValue(), array_slice($emails, 1)),
            'phone' => $primaryPhone?->getValue() ?? null,
            'additional_phones' => array_map(fn($p) => $p->getValue(), array_slice($phoneNumbers, 1)),
        ];
    }

    /**
     * Update or create a contact by email.
     *
     * @param string $email
     * @param array $data
     * @return Person
     * @throws Exception
     */
    public function updateOrCreate(string $email, array $data): Person
    {
        $contact = $this->findContactByEmail($email);

        if ($contact) {
            return $this->updateContact($contact['resource_name'], $data);
        }

        return $this->addContact(
            $data['givenName'] ?? '',
            $data['familyName'] ?? '',
            $email
        );
    }
}
