<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class CharacterAppearancesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('character_appearances');
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
            ->scalar('height')
            ->maxLength('height', 64)
            ->allowEmptyString('height');

        $validator
            ->scalar('weight')
            ->maxLength('weight', 64)
            ->allowEmptyString('weight');

        $validator
            ->scalar('build')
            ->maxLength('build', 128)
            ->allowEmptyString('build');

        $validator
            ->scalar('hair_color')
            ->maxLength('hair_color', 128)
            ->allowEmptyString('hair_color');

        $validator
            ->scalar('hair_style')
            ->allowEmptyString('hair_style');

        $validator
            ->scalar('eye_color')
            ->maxLength('eye_color', 128)
            ->allowEmptyString('eye_color');

        $validator
            ->scalar('skin_description')
            ->allowEmptyString('skin_description');

        $validator
            ->scalar('facial_features')
            ->allowEmptyString('facial_features');

        $validator
            ->scalar('scars')
            ->allowEmptyString('scars');

        $validator
            ->scalar('clothing_style')
            ->allowEmptyString('clothing_style');

        $validator
            ->scalar('health')
            ->allowEmptyString('health');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['character_id'], 'Characters'), ['errorField' => 'character_id']);
        $rules->add($rules->isUnique(['character_id']), ['errorField' => 'character_id']);

        return $rules;
    }
}
