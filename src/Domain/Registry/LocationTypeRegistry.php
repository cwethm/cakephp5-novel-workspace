<?php
declare(strict_types=1);

namespace App\Domain\Registry;

final class LocationTypeRegistry
{
    private const TYPES = [
        'settlement' => 'Settlement',
        'structure' => 'Structure',
        'home' => 'Home',
        'body_of_water' => 'Body Of Water',
        'geological_feature' => 'Geological Feature',
        'place' => 'Place',
        'non_terrestrial' => 'Non Terrestrial',
        'terrain' => 'Terrain',
    ];

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return self::TYPES;
    }

    public static function has(string $type): bool
    {
        return isset(self::TYPES[$type]);
    }
}
