<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class UsersTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('users');
        $this->addBehavior('Timestamp');
        $this->hasMany('Novels');
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('email')
            ->email('email')
            ->requirePresence('email', 'create')
            ->notEmptyString('email');

        $validator
            ->scalar('password')
            ->requirePresence('password', 'create')
            ->notEmptyString('password');

        $validator
            ->scalar('display_name')
            ->maxLength('display_name', 100)
            ->requirePresence('display_name', 'create')
            ->notEmptyString('display_name');

        $validator
            ->scalar('status')
            ->inList('status', ['active', 'disabled'])
            ->notEmptyString('status');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(['email']), ['errorField' => 'email']);

        return $rules;
    }
}
