<?php
declare(strict_types=1);

namespace App\Test\TestCase\Database;

use Cake\Datasource\ConnectionManager;
use Cake\TestSuite\TestCase;

class PhaseSchemaTest extends TestCase
{
    public function testPhaseZeroOneSchemaBoundaries(): void
    {
        $connection = ConnectionManager::get('test');
        $tables = $connection->getSchemaCollection()->listTables();

        $requiredDomainTables = [
            'users',
            'novels',
            'cards',
            'tags',
            'cards_tags',
        ];

        foreach ($requiredDomainTables as $table) {
            $this->assertContains($table, $tables, "Missing required Phase 0/1 table: {$table}");
        }

        $forbiddenLaterPhaseTables = [
            'characters',
            'relationships',
            'chapters',
            'scenes',
            'plot_points',
            'notes',
            'assets',
        ];

        foreach ($forbiddenLaterPhaseTables as $table) {
            $this->assertNotContains($table, $tables, "Found forbidden later-phase table: {$table}");
        }
    }
}
