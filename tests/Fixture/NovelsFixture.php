<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class NovelsFixture extends TestFixture
{
    public array $records = [
        [
            'id' => 1,
            'user_id' => 1,
            'title' => 'The Whitehope Affair',
            'subtitle' => null,
            'author_name' => 'User A',
            'description' => 'Primary novel',
            'status' => 'drafting',
            'created' => '2026-01-01 00:00:00',
            'modified' => '2026-01-01 00:00:00',
        ],
        [
            'id' => 2,
            'user_id' => 2,
            'title' => 'Decoy Novel',
            'subtitle' => null,
            'author_name' => 'User B',
            'description' => 'Secondary novel',
            'status' => 'planning',
            'created' => '2026-01-01 00:00:00',
            'modified' => '2026-01-01 00:00:00',
        ],
    ];
}
