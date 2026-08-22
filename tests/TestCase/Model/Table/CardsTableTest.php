<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

class CardsTableTest extends TestCase
{
    protected array $fixtures = ['app.Users', 'app.Novels', 'app.Cards'];

    public function testCardSlugUniquePerNovel(): void
    {
        $cards = TableRegistry::getTableLocator()->get('Cards');
        $entity = $cards->newEntity([
            'novel_id' => 1,
            'card_type' => 'character',
            'name' => 'Whitehope',
            'slug' => 'whitehope',
            'status' => 'active',
        ]);

        $this->assertFalse($cards->save($entity));
    }

    public function testSameSlugAllowedAcrossNovels(): void
    {
        $cards = TableRegistry::getTableLocator()->get('Cards');
        $entity = $cards->newEntity([
            'novel_id' => 1,
            'card_type' => 'character',
            'name' => 'Different Name',
            'slug' => 'unique-new',
            'status' => 'active',
        ]);
        $this->assertNotFalse($cards->save($entity));

        $entity2 = $cards->newEntity([
            'novel_id' => 2,
            'card_type' => 'character',
            'name' => 'Different Name',
            'slug' => 'unique-new',
            'status' => 'active',
        ]);
        $this->assertNotFalse($cards->save($entity2));
    }

    public function testInvalidCardTypeRejected(): void
    {
        $cards = TableRegistry::getTableLocator()->get('Cards');
        $entity = $cards->newEntity([
            'novel_id' => 1,
            'card_type' => 'planet',
            'name' => 'Bad',
            'status' => 'active',
        ]);

        $this->assertNotEmpty($entity->getErrors()['card_type'] ?? []);
    }

    public function testCardBelongsToExistingNovel(): void
    {
        $cards = TableRegistry::getTableLocator()->get('Cards');
        $entity = $cards->newEntity([
            'novel_id' => 999,
            'card_type' => 'character',
            'name' => 'Ghost',
            'status' => 'active',
        ]);

        $this->assertFalse($cards->save($entity));
    }
}
