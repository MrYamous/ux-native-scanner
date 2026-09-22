<?php
declare(strict_types=1);

namespace App\Service;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\UX\Autocomplete\AutocompleteResults;
use Symfony\UX\Autocomplete\AutocompleterInterface;

#[AutoconfigureTag('ux.autocompleter', ['alias' => 'city'])]
final class CityAutocompleter implements AutocompleterInterface
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
    ) {
    }

    public function fetchResults(string $query, int $page): AutocompleteResults
    {
        if ('' === trim($query)) {
            return new AutocompleteResults([], false);
        }

        $data = $this->httpClient->request('GET', 'https://api-adresse.data.gouv.fr/search/', [
            'query' => [
                'q' => $query,
                'type' => 'municipality',
                'autocomplete' => 1,
                'limit' => 10,
            ],
        ])->toArray();

        $results = [];
        foreach ($data['features'] ?? [] as $feature) {
            $props = $feature['properties'];
            $results[] = [
                'value' => $props['citycode'],
                'text' => $props['city'].' ('.$props['postcode'].')',
            ];
        }

        return new AutocompleteResults($results, false);
    }

    public function isGranted(Security $security): bool
    {
        return true;
    }
}