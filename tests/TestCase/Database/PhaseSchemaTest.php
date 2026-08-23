<?php
declare(strict_types=1);

namespace App\Test\TestCase\Database;

use Cake\Database\Connection;
use Cake\Datasource\ConnectionManager;
use Cake\TestSuite\TestCase;

class PhaseSchemaTest extends TestCase
{
    public function testPhaseTwoSg24SchemaBoundaries(): void
    {
        $connection = ConnectionManager::get('test');
        $this->assertInstanceOf(Connection::class, $connection);
        $tables = $connection->getSchemaCollection()->listTables();

        $requiredDomainTables = [
            'users',
            'novels',
            'cards',
            'tags',
            'cards_tags',
            'characters',
            'character_appearances',
            'character_personalities',
            'character_voices',
            'character_traits',
            'character_skills',
            'character_goals',
            'locations',
            'items',
        ];

        foreach ($requiredDomainTables as $table) {
            $this->assertContains($table, $tables, "Missing required SG-24 table: {$table}");
        }

        $forbiddenLaterPhaseTables = [
            'organizations',
            'character_organizations',
            'relationships',
            'chapters',
            'scenes',
            'characters_scenes',
            'items_scenes',
            'organizations_scenes',
            'story_threads',
            'scenes_story_threads',
            'plot_points',
            'plot_points_story_threads',
            'characters_plot_points',
            'notes',
            'assets',
            'assets_cards',
        ];

        foreach ($forbiddenLaterPhaseTables as $table) {
            $this->assertNotContains($table, $tables, "Found forbidden later-phase table: {$table}");
        }
    }
}
