<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Card extends Entity
{
    protected array $_accessible = [
        'novel_id' => true,
        'card_type' => true,
        'name' => true,
        'slug' => true,
        'short_summary' => true,
        'description' => true,
        'importance' => true,
        'status' => true,
        'sort_order' => true,
        'created' => true,
        'modified' => true,
        'tags' => true,
    ];
}
