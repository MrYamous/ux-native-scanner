<?php
declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\ChoiceList\Loader\AbstractChoiceLoader;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class CityChoiceLoader extends AbstractChoiceLoader
{
    public function __construct(private readonly HttpClientInterface $httpClient)
    {
    }

    protected function loadChoices(): iterable
    {
        return [];
    }

    protected function doLoadChoicesForValues(array $values, ?callable $value): array
    {
        $choices = [];
        foreach ($values as $code) {
            $choices[$this->labelFor($code)] = $code;
        }

        return $choices;
    }

    private function labelFor(string $code): string
    {
        $data = $this->httpClient->request('GET', 'https://geo.api.gouv.fr/communes/'.$code, [
            'query' => ['fields' => 'nom,codesPostaux'],
        ])->toArray(false);

        return $data['nom'] ?? $code;
    }
}