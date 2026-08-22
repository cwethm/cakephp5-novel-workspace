<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

class TagsTableTest extends TestCase
{
    protected array $fixtures = ['app.Users', 'app.Novels', 'app.Tags', 'app.Cards', 'app.CardsTags'];

    public function testTagSlugUniqueWithinNovel(): void
    {
        $tags = TableRegistry::getTableLocator()->get('Tags');
        $entity = $tags->newEntity([
            'novel_id' => 1,
            'name' => 'Lead',
            'slug' => 'lead',
        ]);

        $this->assertFalse($tags->save($entity));
    }

    public function testSameSlugAllowedBetweenNovels(): void
    {
        $tags = TableRegistry::getTableLocator()->get('Tags');
        $entity = $tags->newEntity([
            'novel_id' => 1,
            'name' => 'Mystery',
            'slug' => 'mystery',
        ]);
        $this->assertNotFalse($tags->save($entity));

        $entity2 = $tags->newEntity([
            'novel_id' => 2,
            'name' => 'Mystery',
            'slug' => 'mystery',
        ]);
        $this->assertNotFalse($tags->save($entity2));
    }
}
