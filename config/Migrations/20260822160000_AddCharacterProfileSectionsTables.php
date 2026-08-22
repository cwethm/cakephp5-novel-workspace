<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class AddCharacterProfileSectionsTables extends BaseMigration
{
    public function change(): void
    {
        $this->table('character_appearances', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'biginteger', ['identity' => true, 'signed' => false])
            ->addColumn('character_id', 'biginteger', ['signed' => false])
            ->addColumn('height', 'string', ['limit' => 64, 'null' => true])
            ->addColumn('weight', 'string', ['limit' => 64, 'null' => true])
            ->addColumn('build', 'string', ['limit' => 128, 'null' => true])
            ->addColumn('hair_color', 'string', ['limit' => 128, 'null' => true])
            ->addColumn('hair_style', 'text', ['null' => true])
            ->addColumn('eye_color', 'string', ['limit' => 128, 'null' => true])
            ->addColumn('skin_description', 'text', ['null' => true])
            ->addColumn('facial_features', 'text', ['null' => true])
            ->addColumn('scars', 'text', ['null' => true])
            ->addColumn('clothing_style', 'text', ['null' => true])
            ->addColumn('health', 'text', ['null' => true])
            ->addColumn('created', 'datetime')
            ->addColumn('modified', 'datetime')
            ->addIndex(['character_id'], ['unique' => true])
            ->addForeignKey('character_id', 'characters', 'id', ['delete' => 'CASCADE'])
            ->create();

        $this->table('character_personalities', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'biginteger', ['identity' => true, 'signed' => false])
            ->addColumn('character_id', 'biginteger', ['signed' => false])
            ->addColumn('public_self', 'text', ['null' => true])
            ->addColumn('private_self', 'text', ['null' => true])
            ->addColumn('greatest_fear', 'text', ['null' => true])
            ->addColumn('greatest_desire', 'text', ['null' => true])
            ->addColumn('wants', 'text', ['null' => true])
            ->addColumn('needs', 'text', ['null' => true])
            ->addColumn('response_to_praise', 'text', ['null' => true])
            ->addColumn('response_to_conflict', 'text', ['null' => true])
            ->addColumn('competitiveness', 'text', ['null' => true])
            ->addColumn('neurotype_notes', 'text', ['null' => true])
            ->addColumn('created', 'datetime')
            ->addColumn('modified', 'datetime')
            ->addIndex(['character_id'], ['unique' => true])
            ->addForeignKey('character_id', 'characters', 'id', ['delete' => 'CASCADE'])
            ->create();

        $this->table('character_voices', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'biginteger', ['identity' => true, 'signed' => false])
            ->addColumn('character_id', 'biginteger', ['signed' => false])
            ->addColumn('vocabulary_level', 'string', ['limit' => 128, 'null' => true])
            ->addColumn('education_level', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('accent', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('dialect', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('speaking_style', 'text', ['null' => true])
            ->addColumn('cultural_influences', 'text', ['null' => true])
            ->addColumn('religious_influences', 'text', ['null' => true])
            ->addColumn('created', 'datetime')
            ->addColumn('modified', 'datetime')
            ->addIndex(['character_id'], ['unique' => true])
            ->addForeignKey('character_id', 'characters', 'id', ['delete' => 'CASCADE'])
            ->create();
    }
}
