<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class AddItemsTable extends BaseMigration
{
    public function change(): void
    {
        $this->table('items', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'biginteger', ['identity' => true, 'signed' => false])
            ->addColumn('card_id', 'biginteger', ['signed' => false])
            ->addColumn('item_type', 'string', ['limit' => 64, 'null' => true])
            ->addColumn('owner_character_id', 'biginteger', ['signed' => false, 'null' => true])
            ->addColumn('current_location_id', 'biginteger', ['signed' => false, 'null' => true])
            ->addColumn('appearance', 'text', ['null' => true])
            ->addColumn('history', 'text', ['null' => true])
            ->addColumn('significance', 'text', ['null' => true])
            ->addColumn('capabilities', 'text', ['null' => true])
            ->addColumn('is_unique', 'boolean', ['default' => false])
            ->addColumn('created', 'datetime')
            ->addColumn('modified', 'datetime')
            ->addIndex(['card_id'], ['unique' => true])
            ->addIndex(['owner_character_id'])
            ->addIndex(['current_location_id'])
            ->addForeignKey('card_id', 'cards', 'id', ['delete' => 'CASCADE'])
            ->addForeignKey('owner_character_id', 'characters', 'id', ['delete' => 'SET_NULL'])
            ->addForeignKey('current_location_id', 'locations', 'id', ['delete' => 'SET_NULL'])
            ->create();
    }
}
