<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

class CardsControllerTest extends TestCase
{
    use IntegrationTestTrait;

    protected array $fixtures = ['app.Users', 'app.Novels', 'app.Cards', 'app.Tags', 'app.CardsTags'];

    private function loginAsUserA(): void
    {
        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post('/login', ['email' => 'a@example.com', 'password' => 'password123']);
    }

    public function testCardIndexOnlyShowsCurrentNovel(): void
    {
        $this->loginAsUserA();

        $this->get('/novels/1/cards');

        $this->assertResponseOk();
        $this->assertResponseContains('Cards for The Whitehope Affair');
        $this->assertResponseContains('Whitehope');
    }

    public function testForeignCardRouteReturns404(): void
    {
        $this->loginAsUserA();

        $this->get('/novels/1/cards/2/edit');

        $this->assertResponseCode(404);
    }

    public function testForgedNovelIdIgnoredOnCreate(): void
    {
        $this->loginAsUserA();

        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post('/novels/1/cards/add', [
            'name' => 'Forged Card',
            'card_type' => 'character',
            'status' => 'active',
            'novel_id' => 2,
        ]);

        $this->assertRedirectContains('/novels/1/cards');
    }
}
