<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Novel extends Entity
{
    protected array $_accessible = [
        'title' => true,
        'subtitle' => true,
        'author_name' => true,
        'description' => true,
        'status' => true,
        'created' => true,
        'modified' => true,
        'cards' => true,
        'tags' => true,
    ];
}
