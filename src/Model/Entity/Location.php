<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * @property int $id
 * @property int $card_id
 * @property int|null $parent_location_id
 * @property string|null $location_type
 * @property \App\Model\Entity\Card $card
 * @property \App\Model\Entity\Location|null $parent_location
 * @property iterable<\App\Model\Entity\Location> $child_locations
 */
class Location extends Entity
{
    protected array $_accessible = [
        'card_id' => true,
        'parent_location_id' => true,
        'location_type' => true,
        'address' => true,
        'region' => true,
        'country' => true,
        'latitude' => true,
        'longitude' => true,
        'atmosphere' => true,
        'appearance' => true,
        'climate' => true,
        'culture' => true,
        'history' => true,
        'created' => true,
        'modified' => true,
        'card' => true,
        'parent_location' => true,
        'child_locations' => true,
    ];
}
