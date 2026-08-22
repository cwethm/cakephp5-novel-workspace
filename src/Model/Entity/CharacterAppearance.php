<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class CharacterAppearance extends Entity
{
    protected array $_accessible = [
        'character_id' => true,
        'height' => true,
        'weight' => true,
        'build' => true,
        'hair_color' => true,
        'hair_style' => true,
        'eye_color' => true,
        'skin_description' => true,
        'facial_features' => true,
        'scars' => true,
        'clothing_style' => true,
        'health' => true,
        'created' => true,
        'modified' => true,
        'character' => true,
    ];
}
