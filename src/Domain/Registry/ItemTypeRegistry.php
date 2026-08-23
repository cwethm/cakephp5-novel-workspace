<?php
declare(strict_types=1);

namespace App\Domain\Registry;

final class ItemTypeRegistry
{
    private const TYPES = [
        'weapon' => 'Weapon',
        'armor' => 'Armor',
        'clothing' => 'Clothing',
        'accessory' => 'Accessory',
        'tool' => 'Tool',
        'document' => 'Document',
        'consumable' => 'Consumable',
        'currency' => 'Currency',
        'artifact' => 'Artifact',
        'key_item' => 'Key Item',
        'technology' => 'Technology',
        'vehicle' => 'Vehicle',
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
