<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

class AuthenticationControllerTest extends TestCase
{
    use IntegrationTestTrait;

    protected array $fixtures = [
        'app.Users',
        'app.Novels',
    ];

    public function testLoginSucceedsWithValidCredentials(): void
    {
        $this->enableCsrfToken();
        $this->enableSecurityToken();

        $this->post('/login', [
            'email' => 'a@example.com',
            'password' => 'password123',
        ]);

        $this->assertRedirectContains('/novels');
    }

    public function testProtectedRouteRedirectsToLogin(): void
    {
        $this->get('/novels');
        $this->assertRedirectContains('/login');
    }

    public function testInvalidCredentialsFail(): void
    {
        $this->enableCsrfToken();
        $this->enableSecurityToken();

        $this->post('/login', [
            'email' => 'a@example.com',
            'password' => 'wrong',
        ]);

        $this->assertResponseOk();
        $this->assertResponseContains('Invalid email or password');
    }
}
