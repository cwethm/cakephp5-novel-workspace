<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Domain\Registry\ItemTypeRegistry;
use Cake\Datasource\EntityInterface;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * @property \App\Model\Table\CardsTable $Cards
 * @property \App\Model\Table\CharactersTable $OwnerCharacters
 * @property \App\Model\Table\LocationsTable $CurrentLocations
 */
class ItemsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('items');
        $this->addBehavior('Timestamp');

        $this->belongsTo('Cards', [
            'foreignKey' => 'card_id',
        ]);
        $this->belongsTo('OwnerCharacters', [
            'className' => 'Characters',
            'foreignKey' => 'owner_character_id',
        ]);
        $this->belongsTo('CurrentLocations', [
            'className' => 'Locations',
            'foreignKey' => 'current_location_id',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->nonNegativeInteger('card_id')
            ->requirePresence('card_id', 'create')
            ->notEmptyString('card_id');

        $validator
            ->nonNegativeInteger('owner_character_id')
            ->allowEmptyString('owner_character_id');

        $validator
            ->nonNegativeInteger('current_location_id')
            ->allowEmptyString('current_location_id');

        $validator
            ->scalar('item_type')
            ->maxLength('item_type', 64)
            ->allowEmptyString('item_type')
            ->add('item_type', 'validItemType', [
                'rule' => static function (mixed $value): bool {
                    if ($value === null || $value === '') {
                        return true;
                    }

                    return ItemTypeRegistry::has((string)$value);
                },
                'message' => 'Invalid item type',
            ]);

        $validator
            ->scalar('appearance')
            ->allowEmptyString('appearance');

        $validator
            ->scalar('history')
            ->allowEmptyString('history');

        $validator
            ->scalar('significance')
            ->allowEmptyString('significance');

        $validator
            ->scalar('capabilities')
            ->allowEmptyString('capabilities');

        $validator
            ->boolean('is_unique')
            ->allowEmptyString('is_unique');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['card_id'], 'Cards'), ['errorField' => 'card_id']);
        $rules->add($rules->isUnique(['card_id']), ['errorField' => 'card_id']);
        $rules->add(
            $rules->existsIn(['owner_character_id'], 'OwnerCharacters'),
            ['errorField' => 'owner_character_id'],
        );
        $rules->add(
            $rules->existsIn(['current_location_id'], 'CurrentLocations'),
            ['errorField' => 'current_location_id'],
        );
        $rules->add(function (EntityInterface $entity): bool {
            if (!$entity->has('card_id') || (int)$entity->get('card_id') <= 0) {
                return true;
            }

            $card = $this->Cards->find()
                ->select(['id', 'card_type'])
                ->where(['Cards.id' => (int)$entity->get('card_id')])
                ->first();

            return $card !== null && (string)$card->get('card_type') === 'item';
        }, 'itemCardType', [
            'errorField' => 'card_id',
            'message' => 'Card must have type item',
        ]);

        return $rules;
    }
}
