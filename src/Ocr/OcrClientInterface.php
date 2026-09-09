<?php

declare(strict_types=1);

namespace App\Ocr;

use Symfony\Component\HttpFoundation\File\File;

interface OcrClientInterface
{
    public function extractContactData(File|string $image): OcrContactData;
}
