<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class NovelsTable extends Table
{
    private const STATUSES = ['planning', 'drafting', 'revising', 'complete', 'archived'];

    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('novels');
        $this->addBehavior('Timestamp');

        $this->belongsTo('Users');
        $this->hasMany('Cards');
        $this->hasMany('Tags');
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('title')
            ->maxLength('title', 255)
            ->requirePresence('title', 'create')
            ->notEmptyString('title');

        $validator
            ->scalar('status')
            ->inList('status', self::STATUSES)
            ->requirePresence('status', 'create')
            ->notEmptyString('status');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['user_id'], 'Users'), ['errorField' => 'user_id']);

        return $rules;
    }
}
