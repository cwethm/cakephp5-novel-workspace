<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class CharacterPersonality extends Entity
{
    protected array $_accessible = [
        'character_id' => true,
        'public_self' => true,
        'private_self' => true,
        'greatest_fear' => true,
        'greatest_desire' => true,
        'wants' => true,
        'needs' => true,
        'response_to_praise' => true,
        'response_to_conflict' => true,
        'competitiveness' => true,
        'neurotype_notes' => true,
        'created' => true,
        'modified' => true,
        'character' => true,
    ];
}
