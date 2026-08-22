<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class CharacterVoicesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('character_voices');
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
            ->scalar('vocabulary_level')
            ->maxLength('vocabulary_level', 128)
            ->allowEmptyString('vocabulary_level');

        $validator
            ->scalar('education_level')
            ->maxLength('education_level', 255)
            ->allowEmptyString('education_level');

        $validator
            ->scalar('accent')
            ->maxLength('accent', 255)
            ->allowEmptyString('accent');

        $validator
            ->scalar('dialect')
            ->maxLength('dialect', 255)
            ->allowEmptyString('dialect');

        $validator
            ->scalar('speaking_style')
            ->allowEmptyString('speaking_style');

        $validator
            ->scalar('cultural_influences')
            ->allowEmptyString('cultural_influences');

        $validator
            ->scalar('religious_influences')
            ->allowEmptyString('religious_influences');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['character_id'], 'Characters'), ['errorField' => 'character_id']);
        $rules->add($rules->isUnique(['character_id']), ['errorField' => 'character_id']);

        return $rules;
    }
}
