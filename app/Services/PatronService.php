<?php

namespace App\Services;

use App\Models\Patron;
use App\Models\Token;
use Illuminate\Support\Collection;
use Patreon\API;

class PatronService
{
    /**
     * @var API
     */
    private $apiClient;

    /**
     * Create a new PatronService instance.
     */
    public function __construct()
    {
        $token = Token::first();

        if (! $token) {
            throw new \RuntimeException('No Patreon token found in database. Unable to initialize API client.');
        }

        $this->apiClient = new API($token->access);
    }

    /**
     * Generate and store patrons from Patreon API
     */
    public function generatePatrons(): void
    {
        $patronsUrl = $this->buildPatronsUrl();
        $allPatrons = collect();
        $nextLink = $patronsUrl;

        while ($nextLink) {
            $response = $this->apiClient->get_data($nextLink);
            $patrons = $this->processApiResponse($response);
            $allPatrons = $allPatrons->concat($patrons);

            $nextLink = $response['links']['next'] ?? false;
        }

        $this->storePatrons($allPatrons);
    }

    /**
     * Build the patrons URL for the campaign
     */
    private function buildPatronsUrl(): string
    {
        $campaignResponse = $this->apiClient->fetch_campaigns();

        if (! isset($campaignResponse['data'][0]['id'])) {
            throw new \RuntimeException('Failed to fetch campaign ID');
        }

        return sprintf(
            'campaigns/%s/members?page[size]=1000&include=user,currently_entitled_tiers&fields[tier]=title&fields[user]=full_name,vanity&fields[member]=full_name,patron_status',
            $campaignResponse['data'][0]['id']
        );
    }

    /**
     * Process API response and extract patron data
     */
    private function processApiResponse(array $response): Collection
    {
        $patrons = collect($response['data']);
        $included = collect($response['included'] ?? []);

        // Get active patron IDs and their tier IDs
        $activePatronIDs = $patrons
            ->filter(fn ($patron) => $patron['attributes']['patron_status'] === 'active_patron')
            ->map(function ($patron) {
                $userID = $patron['relationships']['user']['data']['id'] ?? null;
                $tierID = ! empty($patron['relationships']['currently_entitled_tiers']['data'])
                    ? $patron['relationships']['currently_entitled_tiers']['data'][0]['id']
                    : '';

                return [
                    'userID' => $userID,
                    'tierID' => $tierID,
                ];
            })
            ->filter(fn ($patron) => ! empty($patron['userID']));

        // Get tiers and transform names if needed
        $tiers = $included
            ->filter(fn ($item) => $item['type'] === 'tier')
            ->map(function ($tier) {
                $title = $tier['attributes']['title'];

                // Convert Adoring Fan to Patron
                if ($title === 'Adoring Fan') {
                    $tier['attributes']['title'] = 'Patron';
                }

                // Convert Champion to Super Patron
                if ($title === 'Champion') {
                    $tier['attributes']['title'] = 'Super Patron';
                }

                return $tier;
            });

        // Build the final patron names array
        $activePatronNames = collect();

        foreach ($included as $user) {
            if ($user['type'] !== 'user') {
                continue;
            }

            foreach ($activePatronIDs as $id) {
                if ($user['id'] === $id['userID']) {
                    $name = ! empty($user['attributes']['vanity'])
                        ? trim($user['attributes']['vanity'])
                        : trim($user['attributes']['full_name']);

                    $tierName = '';
                    foreach ($tiers as $tier) {
                        if ($tier['id'] === $id['tierID']) {
                            $tierName = $tier['attributes']['title'];
                        }
                    }

                    $activePatronNames->push([
                        'name' => $name,
                        'tier' => $tierName,
                    ]);
                }
            }
        }

        return $activePatronNames;
    }

    /**
     * Store the patrons in the database
     */
    private function storePatrons(Collection $patrons): void
    {
        $patronModel = Patron::first() ?? new Patron;
        $patronModel->patrons = $patrons->values()->all();
        $patronModel->save();
    }
}
