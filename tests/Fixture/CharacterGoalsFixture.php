<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class CharacterGoalsFixture extends TestFixture
{
    public array $records = [
        [
            'id' => 1,
            'character_id' => 1,
            'goal_type' => 'external',
            'description' => 'Expose the smuggling ring.',
            'priority' => 0,
            'status' => 'active',
            'created' => '2026-01-01 00:00:00',
            'modified' => '2026-01-01 00:00:00',
        ],
        [
            'id' => 2,
            'character_id' => 2,
            'goal_type' => 'external',
            'description' => 'Protect the harbor district.',
            'priority' => 0,
            'status' => 'active',
            'created' => '2026-01-01 00:00:00',
            'modified' => '2026-01-01 00:00:00',
        ],
    ];
}
