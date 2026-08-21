<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Tag extends Entity
{
    protected array $_accessible = [
        'name' => true,
        'slug' => true,
        'created' => true,
        'modified' => true,
        'cards' => true,
    ];
}
