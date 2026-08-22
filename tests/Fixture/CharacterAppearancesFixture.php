<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class CharacterAppearancesFixture extends TestFixture
{
    public array $records = [
        [
            'id' => 1,
            'character_id' => 1,
            'height' => '178 cm',
            'weight' => null,
            'build' => null,
            'hair_color' => null,
            'hair_style' => null,
            'eye_color' => null,
            'skin_description' => null,
            'facial_features' => null,
            'scars' => null,
            'clothing_style' => null,
            'health' => null,
            'created' => '2026-01-01 00:00:00',
            'modified' => '2026-01-01 00:00:00',
        ],
    ];
}
