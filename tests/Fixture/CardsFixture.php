<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class CardsFixture extends TestFixture
{
    public array $records = [
        [
            'id' => 1,
            'novel_id' => 1,
            'card_type' => 'character',
            'name' => 'Whitehope',
            'slug' => 'whitehope',
            'short_summary' => 'Main lead',
            'description' => 'desc',
            'importance' => 'high',
            'status' => 'active',
            'sort_order' => 1,
            'created' => '2026-01-01 00:00:00',
            'modified' => '2026-01-01 00:00:00',
        ],
        [
            'id' => 2,
            'novel_id' => 2,
            'card_type' => 'character',
            'name' => 'Whitehope',
            'slug' => 'whitehope',
            'short_summary' => 'Duplicate in another novel',
            'description' => 'desc',
            'importance' => 'normal',
            'status' => 'active',
            'sort_order' => 1,
            'created' => '2026-01-01 00:00:00',
            'modified' => '2026-01-01 00:00:00',
        ],
    ];
}
