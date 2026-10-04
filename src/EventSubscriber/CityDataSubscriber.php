<?php

namespace App\EventSubscriber;

use App\Entity\Contact;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Exclude]
final readonly class CityDataSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private HttpClientInterface $httpClient,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::POST_SUBMIT => 'onFormPostSubmit',
        ];
    }

    public function onFormPostSubmit(PostSubmitEvent $event): void
    {
        $data = $event->getData();

        if (!$data instanceof Contact) {
            return;
        }

        $city = $data->getCity();
        if (!$city) {
            return;
        }

        $geoData = $this->resolveGeoData($city);

        $data->setDepartment($geoData['department']);
        $data->setRegion($geoData['region']);
    }

    /**
     * @return array{department: ?string, region: ?string}
     */
    private function resolveGeoData(string $city): array
    {
        $response = $this->httpClient->request('GET', 'https://geo.api.gouv.fr/communes', [
            'query' => [
                'nom' => $city,
                'fields' => 'nom,departement,region',
                'boost' => 'population',
                'limit' => 1,
            ],
        ]);

        $results = $response->toArray(false);

        if (empty($results) || 0 !== strcasecmp($results[0]['nom'], $city)) {
            return ['department' => null, 'region' => null];
        }

        return [
            'department' => $results[0]['departement']['nom'] ?? null,
            'region' => $results[0]['region']['nom'] ?? null,
        ];
    }
}
