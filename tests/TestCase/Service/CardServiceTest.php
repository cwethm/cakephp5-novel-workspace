<?php
declare(strict_types=1);

namespace App\Test\TestCase\Service;

use App\Domain\CurrentNovel;
use App\Service\CardService;
use App\Service\SlugService;
use Cake\Http\Exception\NotFoundException;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

class CardServiceTest extends TestCase
{
    protected array $fixtures = ['app.Users', 'app.Novels', 'app.Cards', 'app.Tags', 'app.CardsTags'];

    public function testCardRenameRegeneratesSlugSafely(): void
    {
        $cards = TableRegistry::getTableLocator()->get('Cards');
        $novels = TableRegistry::getTableLocator()->get('Novels');
        $users = TableRegistry::getTableLocator()->get('Users');

        $card = $cards->get(1);
        $novel = $novels->get(1);
        $user = $users->get(1);

        $service = new CardService(new SlugService());
        $updated = $service->rename(new CurrentNovel($novel, $user), $card, 'Whitehope');

        $this->assertSame('whitehope', $updated->slug);
    }

    public function testCardArchiveDoesNotDeleteRow(): void
    {
        $cards = TableRegistry::getTableLocator()->get('Cards');
        $novels = TableRegistry::getTableLocator()->get('Novels');
        $users = TableRegistry::getTableLocator()->get('Users');

        $card = $cards->get(1);
        $novel = $novels->get(1);
        $user = $users->get(1);

        $service = new CardService(new SlugService());
        $service->archive(new CurrentNovel($novel, $user), $card);

        $this->assertSame('archived', $cards->get(1)->status);
        $this->assertSame(2, $cards->find()->count());
    }

    public function testTagSyncRemainsNovelScoped(): void
    {
        $cards = TableRegistry::getTableLocator()->get('Cards');
        $novels = TableRegistry::getTableLocator()->get('Novels');
        $users = TableRegistry::getTableLocator()->get('Users');

        $card = $cards->get(1, contain: ['Tags']);
        $novel = $novels->get(1);
        $user = $users->get(1);

        $service = new CardService(new SlugService());
        $updated = $service->syncTags(new CurrentNovel($novel, $user), $card, [1]);

        $this->assertCount(1, $updated->tags ?? []);
        $this->assertSame(1, (int)$updated->tags[0]->id);
    }

    public function testForeignNovelTagCannotAttachToCard(): void
    {
        $cards = TableRegistry::getTableLocator()->get('Cards');
        $novels = TableRegistry::getTableLocator()->get('Novels');
        $users = TableRegistry::getTableLocator()->get('Users');

        $card = $cards->get(1, contain: ['Tags']);
        $novel = $novels->get(1);
        $user = $users->get(1);

        $service = new CardService(new SlugService());

        $this->expectException(NotFoundException::class);
        $service->syncTags(new CurrentNovel($novel, $user), $card, [2]);
    }
}
