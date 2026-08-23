<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Domain\Registry\LocationTypeRegistry;
use Cake\Datasource\EntityInterface;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * @property \App\Model\Table\CardsTable $Cards
 * @property \App\Model\Table\LocationsTable $ParentLocations
 * @property \Cake\ORM\Association\HasMany $ChildLocations
 */
class LocationsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('locations');
        $this->addBehavior('Timestamp');

        $this->belongsTo('Cards', [
            'foreignKey' => 'card_id',
        ]);
        $this->belongsTo('ParentLocations', [
            'className' => 'Locations',
            'foreignKey' => 'parent_location_id',
        ]);
        $this->hasMany('ChildLocations', [
            'className' => 'Locations',
            'foreignKey' => 'parent_location_id',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->nonNegativeInteger('card_id')
            ->requirePresence('card_id', 'create')
            ->notEmptyString('card_id');

        $validator
            ->nonNegativeInteger('parent_location_id')
            ->allowEmptyString('parent_location_id');

        $validator
            ->scalar('location_type')
            ->maxLength('location_type', 64)
            ->allowEmptyString('location_type')
            ->add('location_type', 'validLocationType', [
                'rule' => static function (mixed $value): bool {
                    if ($value === null || $value === '') {
                        return true;
                    }

                    return LocationTypeRegistry::has((string)$value);
                },
                'message' => 'Invalid location type',
            ]);

        $validator
            ->scalar('address')
            ->allowEmptyString('address');

        $validator
            ->scalar('region')
            ->maxLength('region', 255)
            ->allowEmptyString('region');

        $validator
            ->scalar('country')
            ->maxLength('country', 255)
            ->allowEmptyString('country');

        $validator
            ->decimal('latitude')
            ->allowEmptyString('latitude');

        $validator
            ->decimal('longitude')
            ->allowEmptyString('longitude');

        $validator
            ->scalar('atmosphere')
            ->allowEmptyString('atmosphere');

        $validator
            ->scalar('appearance')
            ->allowEmptyString('appearance');

        $validator
            ->scalar('climate')
            ->allowEmptyString('climate');

        $validator
            ->scalar('culture')
            ->allowEmptyString('culture');

        $validator
            ->scalar('history')
            ->allowEmptyString('history');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['card_id'], 'Cards'), ['errorField' => 'card_id']);
        $rules->add($rules->isUnique(['card_id']), ['errorField' => 'card_id']);
        $rules->add(
            $rules->existsIn(['parent_location_id'], 'ParentLocations'),
            ['errorField' => 'parent_location_id'],
        );
        $rules->add(function (EntityInterface $entity): bool {
            if (!$entity->has('card_id') || (int)$entity->get('card_id') <= 0) {
                return true;
            }

            $card = $this->Cards->find()
                ->select(['id', 'card_type'])
                ->where(['Cards.id' => (int)$entity->get('card_id')])
                ->first();

            return $card !== null && (string)$card->get('card_type') === 'location';
        }, 'locationCardType', [
            'errorField' => 'card_id',
            'message' => 'Card must have type location',
        ]);

        return $rules;
    }
}
