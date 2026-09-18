<?php
declare(strict_types=1);

namespace App\Enum;

enum ContactTypeEnum: string
{
    case Caviste = 'Caviste';
    case CHR = 'CHR';
    case Agent = 'Agent';
    case Export = 'Export';
    case Autre = 'Autre';
}
