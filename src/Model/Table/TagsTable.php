<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Service\SlugService;
use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class TagsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('tags');
        $this->addBehavior('Timestamp');

        $this->belongsTo('Novels');
        $this->belongsToMany('Cards', [
            'joinTable' => 'cards_tags',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('name')
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

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
            $slugService = new SlugService();
            $entity->set('slug', $slugService->uniqueWithinNovel(
                (string)$entity->get('name'),
                $novelId,
                function (string $slug, int $novelId) use ($entity): bool {
                    $query = $this->find()->where([
                        'Tags.novel_id' => $novelId,
                        'Tags.slug' => $slug,
                    ]);
                    if ($entity->id) {
                        $query->where(['Tags.id !=' => (int)$entity->id]);
                    }

                    return $query->count() > 0;
                },
            ));
        }
    }
}
