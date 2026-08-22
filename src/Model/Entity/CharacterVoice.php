<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class CharacterVoice extends Entity
{
    protected array $_accessible = [
        'character_id' => true,
        'vocabulary_level' => true,
        'education_level' => true,
        'accent' => true,
        'dialect' => true,
        'speaking_style' => true,
        'cultural_influences' => true,
        'religious_influences' => true,
        'created' => true,
        'modified' => true,
        'character' => true,
    ];
}
