<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class UsersFixture extends TestFixture
{
    public array $records = [
        [
            'id' => 1,
            'email' => 'a@example.com',
            'password' => '$2y$10$oD5UIJTr7/LFUWnPHS08eOyMARUZwUCt4mnytgdLg8E173Qjo5xFK',
            'display_name' => 'User A',
            'status' => 'active',
            'created' => '2026-01-01 00:00:00',
            'modified' => '2026-01-01 00:00:00',
        ],
        [
            'id' => 2,
            'email' => 'b@example.com',
            'password' => '$2y$10$oD5UIJTr7/LFUWnPHS08eOyMARUZwUCt4mnytgdLg8E173Qjo5xFK',
            'display_name' => 'User B',
            'status' => 'active',
            'created' => '2026-01-01 00:00:00',
            'modified' => '2026-01-01 00:00:00',
        ],
    ];
}
