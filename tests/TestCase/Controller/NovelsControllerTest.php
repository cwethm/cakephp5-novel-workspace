<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

class NovelsControllerTest extends TestCase
{
    use IntegrationTestTrait;

    protected array $fixtures = ['app.Users', 'app.Novels'];

    private function loginAsUserA(): void
    {
        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post('/login', ['email' => 'a@example.com', 'password' => 'password123']);
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
