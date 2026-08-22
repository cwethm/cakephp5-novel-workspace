<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class CharacterGoal extends Entity
{
    protected array $_accessible = [
        'character_id' => true,
        'goal_type' => true,
        'description' => true,
        'priority' => true,
        'status' => true,
        'created' => true,
        'modified' => true,
        'character' => true,
    ];
}
