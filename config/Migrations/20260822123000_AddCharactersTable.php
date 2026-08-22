<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class AddCharactersTable extends BaseMigration
{
    public function change(): void
    {
        $this->table('characters', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'biginteger', ['identity' => true, 'signed' => false])
            ->addColumn('card_id', 'biginteger', ['signed' => false])
            ->addColumn('role', 'string', ['limit' => 64, 'null' => true])
            ->addColumn('aliases', 'text', ['null' => true])
            ->addColumn('age', 'integer', ['null' => true])
            ->addColumn('birth_date', 'string', ['limit' => 100, 'null' => true])
            ->addColumn('gender', 'string', ['limit' => 100, 'null' => true])
            ->addColumn('pronouns', 'string', ['limit' => 100, 'null' => true])
            ->addColumn('occupation', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('education', 'text', ['null' => true])
            ->addColumn('backstory', 'text', ['null' => true])
            ->addColumn('external_motivation', 'text', ['null' => true])
            ->addColumn('internal_motivation', 'text', ['null' => true])
            ->addColumn('core_motivation', 'text', ['null' => true])
            ->addColumn('central_conflict', 'text', ['null' => true])
            ->addColumn('family_notes', 'text', ['null' => true])
            ->addColumn('friendship_notes', 'text', ['null' => true])
            ->addColumn('culture_notes', 'text', ['null' => true])
            ->addColumn('religion_notes', 'text', ['null' => true])
            ->addColumn('created', 'datetime')
            ->addColumn('modified', 'datetime')
            ->addIndex(['card_id'], ['unique' => true])
            ->addIndex(['role'])
            ->addForeignKey('card_id', 'cards', 'id', ['delete' => 'CASCADE'])
            ->create();
    }
}
