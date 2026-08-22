<?php
declare(strict_types=1);

namespace App\Model\Table;

use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\Http\Exception\NotFoundException;
use Cake\ORM\Table;

class CardsTagsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('cards_tags');
        $this->belongsTo('Cards');
        $this->belongsTo('Tags');
    }

    public function beforeSave(EventInterface $event, EntityInterface $entity, ArrayObject $options): void
    {
        $cards = $this->fetchTable('Cards');
        $tags = $this->fetchTable('Tags');

        $card = $cards->get((int)$entity->get('card_id'));
        $tag = $tags->get((int)$entity->get('tag_id'));

        if ((int)$card->novel_id !== (int)$tag->novel_id) {
            throw new NotFoundException('Tag and card must belong to the same novel.');
        }
    }
}
