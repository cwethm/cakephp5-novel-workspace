<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

class NovelsControllerTest extends TestCase
{
    use IntegrationTestTrait;

    protected array $fixtures = ['app.Users', 'app.Novels'];

    private function loginAsUserA(): void
    {
        $user = TableRegistry::getTableLocator()->get('Users')->get(1);
        $this->session(['Auth' => $user]);
    }

    public function testAuthenticatedUserCanCreateNovel(): void
    {
        $this->loginAsUserA();

        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post('/novels/add', [
            'title' => 'New Novel',
            'status' => 'planning',
            'user_id' => 2,
        ]);

        $this->assertRedirectContains('/novels');
    }

    public function testUserCannotViewAnotherUsersNovel(): void
    {
        $this->loginAsUserA();

        $this->get('/novels/2');
        $this->assertResponseCode(404);
    }
}
