<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * @property int $id
 * @property int $card_id
 * @property string|null $role
 * @property int|null $age
 * @property \App\Model\Entity\Card $card
 */
class Character extends Entity
{
    protected array $_accessible = [
        'card_id' => true,
        'role' => true,
        'aliases' => true,
        'age' => true,
        'birth_date' => true,
        'gender' => true,
        'pronouns' => true,
        'occupation' => true,
        'education' => true,
        'backstory' => true,
        'external_motivation' => true,
        'internal_motivation' => true,
        'core_motivation' => true,
        'central_conflict' => true,
        'family_notes' => true,
        'friendship_notes' => true,
        'culture_notes' => true,
        'religion_notes' => true,
        'created' => true,
        'modified' => true,
        'card' => true,
        'character_traits' => true,
        'character_skills' => true,
        'character_goals' => true,
    ];
}
