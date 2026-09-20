<?php

declare(strict_types=1);

namespace App\Ocr;

class OcrContactData
{
    public function __construct(
        public ?string $email = null,
        public ?string $phone = null,
        public ?string $website = null,
        public ?string $rawText = null,
    ) {
    }
}
