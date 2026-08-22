<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Domain\Registry\CharacterProfileRegistry;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class CharacterGoalsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('character_goals');
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
            ->scalar('goal_type')
            ->maxLength('goal_type', 32)
            ->requirePresence('goal_type', 'create')
            ->notEmptyString('goal_type')
            ->inList('goal_type', array_keys(CharacterProfileRegistry::goalTypeOptions()));

        $validator
            ->scalar('description')
            ->requirePresence('description', 'create')
            ->notEmptyString('description');

        $validator
            ->integer('priority')
            ->allowEmptyString('priority');

        $validator
            ->scalar('status')
            ->maxLength('status', 32)
            ->requirePresence('status', 'create')
            ->notEmptyString('status')
            ->inList('status', array_keys(CharacterProfileRegistry::goalStatusOptions()));

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['character_id'], 'Characters'), ['errorField' => 'character_id']);

        return $rules;
    }
}
