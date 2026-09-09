<?php

declare(strict_types=1);

namespace App\Ocr;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OcrSpaceClient implements OcrClientInterface
{
    public function __construct(
        private readonly HttpClientInterface $client,
        #[Autowire(env: 'OCR_SPACE_ENDPOINT')]
        private readonly string $endpoint = 'https://api.ocr.space/parse/image',
        #[Autowire(env: 'OCR_SPACE_API_KEY')]
        private readonly ?string $apiKey = null,
    ) {
    }

    public function extractContactData(File|string $image): OcrContactData
    {
        $filePath = $image instanceof File ? $image->getPathname() : $image;

        $fileResource = fopen($filePath, 'rb');
        if (!\is_resource($fileResource)) {
            throw new \RuntimeException('Impossible d’ouvrir le fichier pour l’analyse OCR.');
        }

        try {
            $options = [
                'body' => [
                    'apikey' => $this->apiKey ?: 'helloworld',
                    'file' => $fileResource,
                    'language' => 'fre',
                    'isOverlayRequired' => 'false',
                ],
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ];

            $response = $this->client->request('POST', $this->endpoint, $options);

            if (200 !== $response->getStatusCode()) {
                throw new \RuntimeException(sprintf('L’API OCR a répondu avec le code %d.', $response->getStatusCode()));
            }

            $data = $response->toArray(false);

            if (!empty($data['IsErroredOnProcessing'])) {
                $errorMsg = $data['ErrorMessage'][0] ?? 'Erreur lors du traitement OCR.';
                throw new \RuntimeException('Erreur OCR: ' . $errorMsg);
            }

            $rawText = '';
            if (isset($data['ParsedResults'][0]['ParsedText'])) {
                $rawText = trim((string) $data['ParsedResults'][0]['ParsedText']);
            }

            return $this->parseTextToContactData($rawText);
        } finally {
            fclose($fileResource);
        }
    }

    private function parseTextToContactData(string $rawText): OcrContactData
    {
        $data = new OcrContactData(rawText: $rawText);
        if ('' === $rawText) {
            return $data;
        }

        $lines = array_filter(array_map('trim', explode("\n", $rawText)));

        foreach ($lines as $line) {
            // Email
            if (null === $data->email && preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $line, $matches)) {
                $data->email = $matches[0];
                continue;
            }

            // Site web
            if (null === $data->website && preg_match('/(https?:\/\/)?(www\.)?[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}(\/[^\s]*)?/', $line, $matches)) {
                if (!str_contains($line, '@')) {
                    $data->website = $matches[0];
                    continue;
                }
            }

            // Téléphone
            if (null === $data->phone && preg_match('/(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}/', $line, $matches)) {
                $data->phone = $matches[0];
                continue;
            }
        }

        // Tenter d'extraire nom / prénom depuis la première ligne non-email/tel/web
        foreach ($lines as $line) {
            if ($data->email && str_contains($line, $data->email)) continue;
            if ($data->phone && str_contains($line, $data->phone)) continue;
            if ($data->website && str_contains($line, $data->website)) continue;

            $words = explode(' ', $line);
            if (count($words) >= 2 && count($words) <= 3 && null === $data->firstname && null === $data->lastname) {
                $data->firstname = $words[0];
                $data->lastname = implode(' ', array_slice($words, 1));
                break;
            }
        }

        return $data;
    }
}
