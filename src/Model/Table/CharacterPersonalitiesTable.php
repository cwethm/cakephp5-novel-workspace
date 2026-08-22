<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class CharacterPersonalitiesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('character_personalities');
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
            ->scalar('public_self')
            ->allowEmptyString('public_self');

        $validator
            ->scalar('private_self')
            ->allowEmptyString('private_self');

        $validator
            ->scalar('greatest_fear')
            ->allowEmptyString('greatest_fear');

        $validator
            ->scalar('greatest_desire')
            ->allowEmptyString('greatest_desire');

        $validator
            ->scalar('wants')
            ->allowEmptyString('wants');

        $validator
            ->scalar('needs')
            ->allowEmptyString('needs');

        $validator
            ->scalar('response_to_praise')
            ->allowEmptyString('response_to_praise');

        $validator
            ->scalar('response_to_conflict')
            ->allowEmptyString('response_to_conflict');

        $validator
            ->scalar('competitiveness')
            ->allowEmptyString('competitiveness');

        $validator
            ->scalar('neurotype_notes')
            ->allowEmptyString('neurotype_notes');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['character_id'], 'Characters'), ['errorField' => 'character_id']);
        $rules->add($rules->isUnique(['character_id']), ['errorField' => 'character_id']);

        return $rules;
    }
}
