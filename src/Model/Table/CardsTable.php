<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Domain\Registry\CardTypeRegistry;
use App\Service\SlugService;
use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class CardsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('cards');
        $this->addBehavior('Timestamp');

        $this->belongsTo('Novels');
        $this->hasOne('Characters', [
            'foreignKey' => 'card_id',
        ]);
        $this->belongsToMany('Tags', [
            'joinTable' => 'cards_tags',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('name')
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('card_type')
            ->requirePresence('card_type', 'create')
            ->notEmptyString('card_type')
            ->add('card_type', 'validType', [
                'rule' => fn(string $value): bool => CardTypeRegistry::has($value),
                'message' => 'Invalid card type',
            ]);

        $validator
            ->scalar('status')
            ->requirePresence('status', 'create')
            ->inList('status', ['active', 'archived']);

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['novel_id'], 'Novels'), ['errorField' => 'novel_id']);
        $rules->add($rules->isUnique(['novel_id', 'slug']), ['errorField' => 'slug']);

        return $rules;
    }

    public function beforeSave(EventInterface $event, EntityInterface $entity, ArrayObject $options): void
    {
        if ($entity->isDirty('name') || !$entity->slug) {
            $novelId = (int)$entity->get('novel_id');
            if ($novelId > 0) {
                $slugService = new SlugService();
                $entity->set('slug', $slugService->uniqueWithinNovel(
                    (string)$entity->get('name'),
                    $novelId,
                    function (string $slug, int $novelId) use ($entity): bool {
                        $query = $this->find()->where([
                            'Cards.novel_id' => $novelId,
                            'Cards.slug' => $slug,
                        ]);
                        if ($entity->id) {
                            $query->where(['Cards.id !=' => (int)$entity->id]);
                        }

                        return $query->count() > 0;
                    },
                ));
            }
        }
    }

    public function findForNovel(SelectQuery $query, int $novelId): SelectQuery
    {
        return $query->where(['Cards.novel_id' => $novelId]);
    }
}
