<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class CharacterSkill extends Entity
{
    protected array $_accessible = [
        'character_id' => true,
        'name' => true,
        'description' => true,
        'proficiency' => true,
        'sort_order' => true,
        'created' => true,
        'modified' => true,
        'character' => true,
    ];
}
