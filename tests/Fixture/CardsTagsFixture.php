<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class CardsTagsFixture extends TestFixture
{
    public array $records = [
        ['card_id' => 1, 'tag_id' => 1],
        ['card_id' => 2, 'tag_id' => 2],
    ];
}
