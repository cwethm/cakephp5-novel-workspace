<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

class LocationsTableTest extends TestCase
{
    protected array $fixtures = ['app.Users', 'app.Novels', 'app.Cards', 'app.Locations'];

    public function testDuplicateLocationPerCardRejected(): void
    {
        $cards = TableRegistry::getTableLocator()->get('Cards');
        $locations = TableRegistry::getTableLocator()->get('Locations');

        $card = $cards->newEntity([
            'novel_id' => 1,
            'card_type' => 'location',
            'name' => 'Unique Card',
            'status' => 'active',
        ]);
        $cards->saveOrFail($card);

        $first = $locations->newEntity([
            'card_id' => (int)$card->id,
            'location_type' => 'place',
        ]);
        $locations->saveOrFail($first);

        $second = $locations->newEntity([
            'card_id' => (int)$card->id,
            'location_type' => 'home',
        ]);

        $this->assertFalse($locations->save($second));
    }

    public function testNonLocationCardRejected(): void
    {
        $cards = TableRegistry::getTableLocator()->get('Cards');
        $locations = TableRegistry::getTableLocator()->get('Locations');

        $card = $cards->newEntity([
            'novel_id' => 1,
            'card_type' => 'character',
            'name' => 'Wrong Type',
            'status' => 'active',
        ]);
        $cards->saveOrFail($card);

        $entity = $locations->newEntity([
            'card_id' => (int)$card->id,
            'location_type' => 'place',
        ]);

        $this->assertFalse($locations->save($entity));
        $this->assertNotEmpty($entity->getErrors()['card_id'] ?? []);
    }

    public function testLocationTypeMustUseApprovedKey(): void
    {
        $cards = TableRegistry::getTableLocator()->get('Cards');
        $locations = TableRegistry::getTableLocator()->get('Locations');

        $card = $cards->newEntity([
            'novel_id' => 1,
            'card_type' => 'location',
            'name' => 'Typed Place',
            'status' => 'active',
        ]);
        $cards->saveOrFail($card);

        $entity = $locations->newEntity([
            'card_id' => (int)$card->id,
            'location_type' => 'invalid_type',
        ]);

        $this->assertNotEmpty($entity->getErrors()['location_type'] ?? []);
    }
}
