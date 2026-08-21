<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class TagsFixture extends TestFixture
{
    public array $records = [
        [
            'id' => 1,
            'novel_id' => 1,
            'name' => 'Lead',
            'slug' => 'lead',
            'created' => '2026-01-01 00:00:00',
            'modified' => '2026-01-01 00:00:00',
        ],
        [
            'id' => 2,
            'novel_id' => 2,
            'name' => 'Lead',
            'slug' => 'lead',
            'created' => '2026-01-01 00:00:00',
            'modified' => '2026-01-01 00:00:00',
        ],
    ];
}
