<?php
declare(strict_types=1);

namespace App\Domain\Registry;

final class CardTypeRegistry
{
    private const TYPES = [
        'character' => [
            'label' => 'Character',
            'table' => 'Characters',
            'route' => 'characters',
            'icon' => 'person',
        ],
        'location' => [
            'label' => 'Location',
            'table' => 'Locations',
            'route' => 'locations',
            'icon' => 'pin',
        ],
        'item' => [
            'label' => 'Item',
            'table' => 'Items',
            'route' => 'items',
            'icon' => 'cube',
        ],
        'organization' => [
            'label' => 'Organization',
            'table' => 'Organizations',
            'route' => 'organizations',
            'icon' => 'group',
        ],
    ];

    public static function all(): array
    {
        return self::TYPES;
    }

    public static function has(string $type): bool
    {
        return isset(self::TYPES[$type]);
    }

    public static function options(): array
    {
        $out = [];
        foreach (self::TYPES as $key => $meta) {
            $out[$key] = $meta['label'];
        }

        return $out;
    }
}
