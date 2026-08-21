<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Authentication\PasswordHasher\DefaultPasswordHasher;
use Cake\ORM\Entity;

class User extends Entity
{
    protected array $_accessible = [
        'email' => true,
        'password' => true,
        'display_name' => true,
        'status' => true,
        'created' => true,
        'modified' => true,
        'novels' => true,
    ];

    protected array $_hidden = [
        'password',
    ];

    protected function _setPassword(?string $password): ?string
    {
        if ($password === null || $password === '') {
            return null;
        }

        return (new DefaultPasswordHasher())->hash($password);
    }
}
