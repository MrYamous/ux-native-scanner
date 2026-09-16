<?php

declare(strict_types=1);

namespace App\Ocr;

class OcrContactData
{
    public function __construct(
        public ?string $firstname = null,
        public ?string $lastname = null,
        public ?string $email = null,
        public ?string $phone = null,
        public ?string $company = null,
        public ?string $website = null,
        public ?string $address = null,
        public ?string $rawText = null,
    ) {
    }
}
