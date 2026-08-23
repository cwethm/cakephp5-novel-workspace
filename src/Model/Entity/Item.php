<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * @property int $id
 * @property int $card_id
 * @property int|null $owner_character_id
 * @property int|null $current_location_id
 * @property string|null $item_type
 * @property bool $is_unique
 * @property \App\Model\Entity\Card $card
 * @property \App\Model\Entity\Character|null $owner_character
 * @property \App\Model\Entity\Location|null $current_location
 */
class Item extends Entity
{
    protected array $_accessible = [
        'card_id' => true,
        'owner_character_id' => true,
        'current_location_id' => true,
        'item_type' => true,
        'appearance' => true,
        'history' => true,
        'significance' => true,
        'capabilities' => true,
        'is_unique' => true,
        'created' => true,
        'modified' => true,
        'card' => true,
        'owner_character' => true,
        'current_location' => true,
    ];
}
