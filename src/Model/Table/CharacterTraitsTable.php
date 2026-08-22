<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Domain\Registry\CharacterProfileRegistry;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class CharacterTraitsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('character_traits');
        $this->addBehavior('Timestamp');

        $this->belongsTo('Characters', [
            'foreignKey' => 'character_id',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->nonNegativeInteger('character_id')
            ->requirePresence('character_id', 'create')
            ->notEmptyString('character_id');

        $validator
            ->scalar('trait_type')
            ->maxLength('trait_type', 32)
            ->requirePresence('trait_type', 'create')
            ->notEmptyString('trait_type')
            ->inList('trait_type', array_keys(CharacterProfileRegistry::traitTypeOptions()));

        $validator
            ->scalar('name')
            ->maxLength('name', 255)
            ->allowEmptyString('name');

        $validator
            ->scalar('description')
            ->requirePresence('description', 'create')
            ->notEmptyString('description');

        $validator
            ->integer('sort_order')
            ->allowEmptyString('sort_order');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['character_id'], 'Characters'), ['errorField' => 'character_id']);

        return $rules;
    }
}
