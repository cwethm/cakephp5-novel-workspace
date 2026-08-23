<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

class ItemsTableTest extends TestCase
{
    protected array $fixtures = [
        'app.Users',
        'app.Novels',
        'app.Cards',
        'app.Characters',
        'app.Locations',
        'app.Items',
    ];

    public function testDuplicateItemPerCardRejected(): void
    {
        $cards = TableRegistry::getTableLocator()->get('Cards');
        $items = TableRegistry::getTableLocator()->get('Items');

        $card = $cards->newEntity([
            'novel_id' => 1,
            'card_type' => 'item',
            'name' => 'Unique Item Card',
            'status' => 'active',
        ]);
        $cards->saveOrFail($card);

        $first = $items->newEntity([
            'card_id' => (int)$card->id,
            'item_type' => 'tool',
        ]);
        $items->saveOrFail($first);

        $second = $items->newEntity([
            'card_id' => (int)$card->id,
            'item_type' => 'document',
        ]);

        $this->assertFalse($items->save($second));
    }

    public function testNonItemCardRejected(): void
    {
        $cards = TableRegistry::getTableLocator()->get('Cards');
        $items = TableRegistry::getTableLocator()->get('Items');

        $card = $cards->newEntity([
            'novel_id' => 1,
            'card_type' => 'character',
            'name' => 'Wrong Type',
            'status' => 'active',
        ]);
        $cards->saveOrFail($card);

        $entity = $items->newEntity([
            'card_id' => (int)$card->id,
            'item_type' => 'weapon',
        ]);

        $this->assertFalse($items->save($entity));
        $this->assertNotEmpty($entity->getErrors()['card_id'] ?? []);
    }

    public function testItemTypeMustUseApprovedKey(): void
    {
        $cards = TableRegistry::getTableLocator()->get('Cards');
        $items = TableRegistry::getTableLocator()->get('Items');

        $card = $cards->newEntity([
            'novel_id' => 1,
            'card_type' => 'item',
            'name' => 'Typed Item',
            'status' => 'active',
        ]);
        $cards->saveOrFail($card);

        $entity = $items->newEntity([
            'card_id' => (int)$card->id,
            'item_type' => 'invalid_type',
        ]);

        $this->assertNotEmpty($entity->getErrors()['item_type'] ?? []);
    }
}
