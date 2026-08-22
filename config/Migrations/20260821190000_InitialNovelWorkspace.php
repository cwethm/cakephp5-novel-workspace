<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class InitialNovelWorkspace extends BaseMigration
{
    public function change(): void
    {
        $this->table('users', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'biginteger', ['identity' => true, 'signed' => false])
            ->addColumn('email', 'string', ['limit' => 255])
            ->addColumn('password', 'string', ['limit' => 255])
            ->addColumn('display_name', 'string', ['limit' => 100])
            ->addColumn('status', 'string', ['limit' => 20, 'default' => 'active'])
            ->addColumn('created', 'datetime')
            ->addColumn('modified', 'datetime')
            ->addIndex(['email'], ['unique' => true])
            ->create();

        $this->table('novels', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'biginteger', ['identity' => true, 'signed' => false])
            ->addColumn('user_id', 'biginteger', ['signed' => false])
            ->addColumn('title', 'string', ['limit' => 255])
            ->addColumn('subtitle', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('author_name', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('description', 'text', ['null' => true])
            ->addColumn('status', 'string', ['limit' => 20, 'default' => 'planning'])
            ->addColumn('created', 'datetime')
            ->addColumn('modified', 'datetime')
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE'])
            ->addIndex(['user_id', 'status'])
            ->create();

        $this->table('cards', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'biginteger', ['identity' => true, 'signed' => false])
            ->addColumn('novel_id', 'biginteger', ['signed' => false])
            ->addColumn('card_type', 'string', ['limit' => 50])
            ->addColumn('name', 'string', ['limit' => 255])
            ->addColumn('slug', 'string', ['limit' => 255])
            ->addColumn('short_summary', 'string', ['limit' => 500, 'null' => true])
            ->addColumn('description', 'text', ['null' => true])
            ->addColumn('importance', 'string', ['limit' => 20, 'default' => 'normal'])
            ->addColumn('status', 'string', ['limit' => 20, 'default' => 'active'])
            ->addColumn('sort_order', 'integer', ['default' => 0])
            ->addColumn('created', 'datetime')
            ->addColumn('modified', 'datetime')
            ->addForeignKey('novel_id', 'novels', 'id', ['delete' => 'CASCADE'])
            ->addIndex(['novel_id', 'slug'], ['unique' => true])
            ->addIndex(['novel_id', 'card_type'])
            ->create();

        $this->table('tags', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'biginteger', ['identity' => true, 'signed' => false])
            ->addColumn('novel_id', 'biginteger', ['signed' => false])
            ->addColumn('name', 'string', ['limit' => 255])
            ->addColumn('slug', 'string', ['limit' => 255])
            ->addColumn('created', 'datetime')
            ->addColumn('modified', 'datetime')
            ->addForeignKey('novel_id', 'novels', 'id', ['delete' => 'CASCADE'])
            ->addIndex(['novel_id', 'slug'], ['unique' => true])
            ->create();

        $this->table('cards_tags', ['id' => false, 'primary_key' => ['card_id', 'tag_id']])
            ->addColumn('card_id', 'biginteger', ['signed' => false])
            ->addColumn('tag_id', 'biginteger', ['signed' => false])
            ->addForeignKey('card_id', 'cards', 'id', ['delete' => 'CASCADE'])
            ->addForeignKey('tag_id', 'tags', 'id', ['delete' => 'CASCADE'])
            ->addIndex(['card_id', 'tag_id'], ['unique' => true])
            ->create();
    }
}
