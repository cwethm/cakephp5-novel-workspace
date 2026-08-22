<?php
declare(strict_types=1);

namespace App\Domain;

use App\Model\Entity\Novel;
use App\Model\Entity\User;
use Cake\Datasource\EntityInterface;
use Cake\Http\Exception\NotFoundException;

final class CurrentNovel
{
    public function __construct(
        private Novel $novel,
        private ?User $user,
    ) {
    }

    public function id(): int
    {
        return (int)$this->novel->id;
    }

    public function entity(): Novel
    {
        return $this->novel;
    }

    public function user(): ?User
    {
        return $this->user;
    }

    public function assertContains(EntityInterface $entity): void
    {
        if (!$entity->has('novel_id') || (int)$entity->get('novel_id') !== $this->id()) {
            throw new NotFoundException();
        }
    }
}
