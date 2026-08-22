<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

class CharactersTableTest extends TestCase
{
    protected array $fixtures = ['app.Users', 'app.Novels', 'app.Cards', 'app.Characters'];

    public function testDuplicateCharacterPerCardRejected(): void
    {
        $characters = TableRegistry::getTableLocator()->get('Characters');

        $entity = $characters->newEntity([
            'card_id' => 1,
            'role' => 'duplicate',
        ]);

        $this->assertFalse($characters->save($entity));
    }

    public function testNonCharacterCardRejected(): void
    {
        $cards = TableRegistry::getTableLocator()->get('Cards');
        $characters = TableRegistry::getTableLocator()->get('Characters');

        $card = $cards->newEntity([
            'novel_id' => 1,
            'card_type' => 'location',
            'name' => 'Old Tower',
            'status' => 'active',
        ]);
        $cards->saveOrFail($card);

        $entity = $characters->newEntity([
            'card_id' => (int)$card->id,
            'role' => 'invalid',
        ]);

        $this->assertFalse($characters->save($entity));
        $this->assertNotEmpty($entity->getErrors()['card_id'] ?? []);
    }

    public function testAgeMustBeIntegerWhenProvided(): void
    {
        $characters = TableRegistry::getTableLocator()->get('Characters');

        $entity = $characters->newEntity([
            'card_id' => 1,
            'age' => 'not-an-integer',
        ]);

        $this->assertNotEmpty($entity->getErrors()['age'] ?? []);
    }
}
