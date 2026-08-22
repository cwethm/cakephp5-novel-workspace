<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\Datasource\EntityInterface;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * @property \App\Model\Table\CardsTable $Cards
 */
class CharactersTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('characters');
        $this->addBehavior('Timestamp');

        $this->belongsTo('Cards', [
            'foreignKey' => 'card_id',
        ]);
        $this->hasOne('CharacterAppearances', [
            'foreignKey' => 'character_id',
            'dependent' => true,
        ]);
        $this->hasOne('CharacterPersonalities', [
            'foreignKey' => 'character_id',
            'dependent' => true,
        ]);
        $this->hasOne('CharacterVoices', [
            'foreignKey' => 'character_id',
            'dependent' => true,
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->nonNegativeInteger('card_id')
            ->requirePresence('card_id', 'create')
            ->notEmptyString('card_id');

        $validator
            ->scalar('role')
            ->maxLength('role', 64)
            ->allowEmptyString('role');

        $validator
            ->scalar('aliases')
            ->allowEmptyString('aliases');

        $validator
            ->integer('age')
            ->allowEmptyString('age');

        $validator
            ->scalar('birth_date')
            ->maxLength('birth_date', 100)
            ->allowEmptyString('birth_date');

        $validator
            ->scalar('gender')
            ->maxLength('gender', 100)
            ->allowEmptyString('gender');

        $validator
            ->scalar('pronouns')
            ->maxLength('pronouns', 100)
            ->allowEmptyString('pronouns');

        $validator
            ->scalar('occupation')
            ->maxLength('occupation', 255)
            ->allowEmptyString('occupation');

        $validator
            ->scalar('education')
            ->allowEmptyString('education');

        $validator
            ->scalar('backstory')
            ->allowEmptyString('backstory');

        $validator
            ->scalar('external_motivation')
            ->allowEmptyString('external_motivation');

        $validator
            ->scalar('internal_motivation')
            ->allowEmptyString('internal_motivation');

        $validator
            ->scalar('core_motivation')
            ->allowEmptyString('core_motivation');

        $validator
            ->scalar('central_conflict')
            ->allowEmptyString('central_conflict');

        $validator
            ->scalar('family_notes')
            ->allowEmptyString('family_notes');

        $validator
            ->scalar('friendship_notes')
            ->allowEmptyString('friendship_notes');

        $validator
            ->scalar('culture_notes')
            ->allowEmptyString('culture_notes');

        $validator
            ->scalar('religion_notes')
            ->allowEmptyString('religion_notes');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['card_id'], 'Cards'), ['errorField' => 'card_id']);
        $rules->add($rules->isUnique(['card_id']), ['errorField' => 'card_id']);
        $rules->add(function (EntityInterface $entity): bool {
            if (!$entity->has('card_id') || (int)$entity->get('card_id') <= 0) {
                return true;
            }

            $card = $this->Cards->find()
                ->select(['id', 'card_type'])
                ->where(['Cards.id' => (int)$entity->get('card_id')])
                ->first();

            return $card !== null && (string)$card->get('card_type') === 'character';
        }, 'characterCardType', [
            'errorField' => 'card_id',
            'message' => 'Card must have type character',
        ]);

        return $rules;
    }
}
