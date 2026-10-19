<?php

declare(strict_types=1);

namespace App\Native;

use Symfony\UX\Native\Attribute\AsNativeConfiguration;
use Symfony\UX\Native\Attribute\AsNativeConfigurationProvider;
use Symfony\UX\Native\Configuration\Configuration;
use Symfony\UX\Native\Configuration\Rule;

#[AsNativeConfigurationProvider]
final class AppNativeConfiguration
{
    /**
     * Configuration principale pour iOS.
     * Servie en /config/ios.json (statique via build, ou dynamiquement en dev).
     */
    #[AsNativeConfiguration('/config/ios.json')]
    public function ios(): Configuration
    {
        return new Configuration(
            settings: [
                'screenshots' => 'replace',
            ],
            rules: [
                // La page de scan ouvre en modal natif
                new Rule(
                    patterns: ['/scan'],
                    properties: ['presentation' => 'modal'],
                ),
                // Le formulaire de review aussi
                new Rule(
                    patterns: ['/scan/save'],
                    properties: ['presentation' => 'modal'],
                ),
                // Les fiches contact s'ouvrent en push standard
                new Rule(
                    patterns: ['/contacts/.*'],
                    properties: ['presentation' => 'default'],
                ),
            ],
        );
    }
}
