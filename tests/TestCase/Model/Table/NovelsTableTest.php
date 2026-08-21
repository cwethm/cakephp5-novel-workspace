<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

class NovelsTableTest extends TestCase
{
    protected array $fixtures = ['app.Users', 'app.Novels'];

    public function testTitleRequired(): void
    {
        $novels = TableRegistry::getTableLocator()->get('Novels');
        $entity = $novels->newEntity(['status' => 'planning', 'user_id' => 1]);
        $this->assertNotEmpty($entity->getErrors()['title'] ?? []);
    }

    public function testValidStatusRequired(): void
    {
        $novels = TableRegistry::getTableLocator()->get('Novels');
        $entity = $novels->newEntity(['title' => 'Test', 'status' => 'invalid', 'user_id' => 1]);
        $this->assertNotEmpty($entity->getErrors()['status'] ?? []);
    }

    public function testArchivedNovelRemainsStored(): void
    {
        $novels = TableRegistry::getTableLocator()->get('Novels');
        $novel = $novels->get(1);
        $novel->status = 'archived';
        $novels->saveOrFail($novel);

        $fetched = $novels->get(1);
        $this->assertSame('archived', $fetched->status);
    }
}
