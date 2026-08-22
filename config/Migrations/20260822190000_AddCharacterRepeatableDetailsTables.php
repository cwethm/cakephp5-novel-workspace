<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class AddCharacterRepeatableDetailsTables extends BaseMigration
{
    public function change(): void
    {
        $this->table('character_traits', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'biginteger', ['identity' => true, 'signed' => false])
            ->addColumn('character_id', 'biginteger', ['signed' => false])
            ->addColumn('trait_type', 'string', ['limit' => 32])
            ->addColumn('name', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('description', 'text')
            ->addColumn('sort_order', 'integer', ['default' => 0])
            ->addColumn('created', 'datetime')
            ->addColumn('modified', 'datetime')
            ->addIndex(['character_id', 'trait_type'])
            ->addForeignKey('character_id', 'characters', 'id', ['delete' => 'CASCADE'])
            ->create();

        $this->table('character_skills', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'biginteger', ['identity' => true, 'signed' => false])
            ->addColumn('character_id', 'biginteger', ['signed' => false])
            ->addColumn('name', 'string', ['limit' => 255])
            ->addColumn('description', 'text', ['null' => true])
            ->addColumn('proficiency', 'string', ['limit' => 64, 'null' => true])
            ->addColumn('sort_order', 'integer', ['default' => 0])
            ->addColumn('created', 'datetime')
            ->addColumn('modified', 'datetime')
            ->addIndex(['character_id'])
            ->addForeignKey('character_id', 'characters', 'id', ['delete' => 'CASCADE'])
            ->create();

        $this->table('character_goals', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'biginteger', ['identity' => true, 'signed' => false])
            ->addColumn('character_id', 'biginteger', ['signed' => false])
            ->addColumn('goal_type', 'string', ['limit' => 32, 'default' => 'external'])
            ->addColumn('description', 'text')
            ->addColumn('priority', 'integer', ['default' => 0])
            ->addColumn('status', 'string', ['limit' => 32, 'default' => 'active'])
            ->addColumn('created', 'datetime')
            ->addColumn('modified', 'datetime')
            ->addIndex(['character_id', 'status'])
            ->addForeignKey('character_id', 'characters', 'id', ['delete' => 'CASCADE'])
            ->create();
    }
}
