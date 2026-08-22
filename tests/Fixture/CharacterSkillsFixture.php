<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class CharacterSkillsFixture extends TestFixture
{
    public array $records = [
        [
            'id' => 1,
            'character_id' => 1,
            'name' => 'Investigation',
            'description' => 'Follows complex evidence trails.',
            'proficiency' => 'expert',
            'sort_order' => 0,
            'created' => '2026-01-01 00:00:00',
            'modified' => '2026-01-01 00:00:00',
        ],
        [
            'id' => 2,
            'character_id' => 2,
            'name' => 'Persuasion',
            'description' => 'Builds rapport quickly.',
            'proficiency' => 'advanced',
            'sort_order' => 0,
            'created' => '2026-01-01 00:00:00',
            'modified' => '2026-01-01 00:00:00',
        ],
    ];
}
