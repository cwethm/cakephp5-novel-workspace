<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class AddLocationsTable extends BaseMigration
{
    public function change(): void
    {
        $this->table('locations', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'biginteger', ['identity' => true, 'signed' => false])
            ->addColumn('card_id', 'biginteger', ['signed' => false])
            ->addColumn('parent_location_id', 'biginteger', ['signed' => false, 'null' => true])
            ->addColumn('location_type', 'string', ['limit' => 64, 'null' => true])
            ->addColumn('address', 'text', ['null' => true])
            ->addColumn('region', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('country', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('latitude', 'decimal', ['precision' => 10, 'scale' => 7, 'null' => true])
            ->addColumn('longitude', 'decimal', ['precision' => 10, 'scale' => 7, 'null' => true])
            ->addColumn('atmosphere', 'text', ['null' => true])
            ->addColumn('appearance', 'text', ['null' => true])
            ->addColumn('climate', 'text', ['null' => true])
            ->addColumn('culture', 'text', ['null' => true])
            ->addColumn('history', 'text', ['null' => true])
            ->addColumn('created', 'datetime')
            ->addColumn('modified', 'datetime')
            ->addIndex(['card_id'], ['unique' => true])
            ->addIndex(['parent_location_id'])
            ->addForeignKey('card_id', 'cards', 'id', ['delete' => 'CASCADE'])
            ->addForeignKey('parent_location_id', 'locations', 'id', ['delete' => 'SET_NULL'])
            ->create();
    }
}
