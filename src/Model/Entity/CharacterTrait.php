<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class CharacterTrait extends Entity
{
    protected array $_accessible = [
        'character_id' => true,
        'trait_type' => true,
        'name' => true,
        'description' => true,
        'sort_order' => true,
        'created' => true,
        'modified' => true,
        'character' => true,
    ];
}
