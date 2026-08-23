<?php
declare(strict_types=1);

namespace App\Test\TestCase\Service;

use App\Domain\CurrentNovel;
use App\Service\LocationService;
use Cake\Http\Exception\NotFoundException;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

class LocationServiceTest extends TestCase
{
    protected array $fixtures = [
        'app.Users',
        'app.Novels',
        'app.Cards',
        'app.Locations',
    ];

    private function currentNovelOne(): CurrentNovel
    {
        $novels = TableRegistry::getTableLocator()->get('Novels');
        $users = TableRegistry::getTableLocator()->get('Users');

        return new CurrentNovel($novels->get(1), $users->get(1));
    }

    public function testCreatePersistsCardAndLocation(): void
    {
        $service = new LocationService();

        $created = $service->create(
            $this->currentNovelOne(),
            [
                'name' => 'New Location Card',
                'status' => 'active',
                'novel_id' => 2,
                'card_type' => 'character',
            ],
            [
                'location_type' => 'settlement',
                'region' => 'North',
            ],
        );

        $cards = TableRegistry::getTableLocator()->get('Cards');
        $locations = TableRegistry::getTableLocator()->get('Locations');

        $card = $cards->get((int)$created->card_id);
        $this->assertSame(1, (int)$card->novel_id);
        $this->assertSame('location', (string)$card->card_type);

        $this->assertTrue($locations->exists(['id' => (int)$created->id]));
        $this->assertSame('settlement', (string)$created->location_type);
    }

    public function testCreateIsAtomicWhenLocationValidationFails(): void
    {
        $service = new LocationService();
        $cards = TableRegistry::getTableLocator()->get('Cards');

        $before = $cards->find()->count();

        try {
            $service->create(
                $this->currentNovelOne(),
                [
                    'name' => 'Should Rollback',
                    'status' => 'active',
                ],
                [
                    'location_type' => 'unknown_type',
                ],
            );
            $this->fail('Expected PersistenceFailedException was not thrown.');
        } catch (PersistenceFailedException) {
            $this->assertSame($before, $cards->find()->count());
        }
    }

    public function testInitializeRejectsForeignNovelCard(): void
    {
        $service = new LocationService();

        $this->expectException(NotFoundException::class);
        $service->initializeForCard($this->currentNovelOne(), 2, ['location_type' => 'place']);
    }

    public function testInitializeRejectsNonLocationCard(): void
    {
        $cards = TableRegistry::getTableLocator()->get('Cards');
        $service = new LocationService();

        $card = $cards->newEntity([
            'novel_id' => 1,
            'card_type' => 'character',
            'name' => 'Wrong Type Card',
            'status' => 'active',
        ]);
        $cards->saveOrFail($card);

        $this->expectException(NotFoundException::class);
        $service->initializeForCard($this->currentNovelOne(), (int)$card->id, ['location_type' => 'place']);
    }

    public function testInitializeRejectsDuplicateLocationPerCard(): void
    {
        $cards = TableRegistry::getTableLocator()->get('Cards');
        $locations = TableRegistry::getTableLocator()->get('Locations');

        $card = $cards->newEntity([
            'novel_id' => 1,
            'card_type' => 'location',
            'name' => 'Already Initialized',
            'status' => 'active',
        ]);
        $cards->saveOrFail($card);

        $location = $locations->newEntity([
            'card_id' => (int)$card->id,
            'location_type' => 'place',
        ]);
        $locations->saveOrFail($location);

        $service = new LocationService();
        $this->expectException(NotFoundException::class);
        $service->initializeForCard($this->currentNovelOne(), (int)$card->id, ['location_type' => 'home']);
    }

    public function testUpdateRejectsSelfParent(): void
    {
        $cards = TableRegistry::getTableLocator()->get('Cards');
        $locations = TableRegistry::getTableLocator()->get('Locations');

        $card = $cards->newEntity([
            'novel_id' => 1,
            'card_type' => 'location',
            'name' => 'Self Parent Candidate',
            'status' => 'active',
        ]);
        $cards->saveOrFail($card);

        $location = $locations->newEntity([
            'card_id' => (int)$card->id,
            'location_type' => 'place',
        ]);
        $locations->saveOrFail($location);

        $service = new LocationService();

        try {
            $service->update(
                $this->currentNovelOne(),
                (int)$location->id,
                ['name' => 'Should Rollback'],
                ['parent_location_id' => (int)$location->id],
            );
            $this->fail('Expected PersistenceFailedException was not thrown.');
        } catch (PersistenceFailedException $exception) {
            $this->assertNotEmpty($exception->getEntity()->getErrors()['parent_location_id'] ?? []);
            $this->assertSame('Self Parent Candidate', (string)$cards->get((int)$card->id)->name);
        }
    }

    public function testUpdateRejectsHierarchyCycle(): void
    {
        $cards = TableRegistry::getTableLocator()->get('Cards');
        $locations = TableRegistry::getTableLocator()->get('Locations');

        $parentCard = $cards->newEntity([
            'novel_id' => 1,
            'card_type' => 'location',
            'name' => 'Parent',
            'status' => 'active',
        ]);
        $cards->saveOrFail($parentCard);

        $childCard = $cards->newEntity([
            'novel_id' => 1,
            'card_type' => 'location',
            'name' => 'Child',
            'status' => 'active',
        ]);
        $cards->saveOrFail($childCard);

        $parent = $locations->newEntity([
            'card_id' => (int)$parentCard->id,
            'location_type' => 'place',
        ]);
        $locations->saveOrFail($parent);

        $child = $locations->newEntity([
            'card_id' => (int)$childCard->id,
            'location_type' => 'home',
            'parent_location_id' => (int)$parent->id,
        ]);
        $locations->saveOrFail($child);

        $service = new LocationService();

        try {
            $service->update(
                $this->currentNovelOne(),
                (int)$parent->id,
                ['name' => 'Parent Renamed'],
                ['parent_location_id' => (int)$child->id],
            );
            $this->fail('Expected PersistenceFailedException was not thrown.');
        } catch (PersistenceFailedException $exception) {
            $this->assertNotEmpty($exception->getEntity()->getErrors()['parent_location_id'] ?? []);
            $this->assertSame('Parent', (string)$cards->get((int)$parentCard->id)->name);
            $this->assertNull($locations->get((int)$parent->id)->parent_location_id);
        }
    }

    public function testUpdateRejectsForeignNovelParent(): void
    {
        $cards = TableRegistry::getTableLocator()->get('Cards');
        $locations = TableRegistry::getTableLocator()->get('Locations');

        $localCard = $cards->newEntity([
            'novel_id' => 1,
            'card_type' => 'location',
            'name' => 'Local Location',
            'status' => 'active',
        ]);
        $cards->saveOrFail($localCard);

        $foreignCard = $cards->newEntity([
            'novel_id' => 2,
            'card_type' => 'location',
            'name' => 'Foreign Location',
            'status' => 'active',
        ]);
        $cards->saveOrFail($foreignCard);

        $local = $locations->newEntity([
            'card_id' => (int)$localCard->id,
            'location_type' => 'place',
        ]);
        $locations->saveOrFail($local);

        $foreign = $locations->newEntity([
            'card_id' => (int)$foreignCard->id,
            'location_type' => 'terrain',
        ]);
        $locations->saveOrFail($foreign);

        $service = new LocationService();

        $this->expectException(NotFoundException::class);
        $service->update(
            $this->currentNovelOne(),
            (int)$local->id,
            ['name' => 'Local Location'],
            ['parent_location_id' => (int)$foreign->id],
        );
    }

    public function testParentOptionsStayNovelScoped(): void
    {
        $cards = TableRegistry::getTableLocator()->get('Cards');
        $locations = TableRegistry::getTableLocator()->get('Locations');

        $localCard = $cards->newEntity([
            'novel_id' => 1,
            'card_type' => 'location',
            'name' => 'Dock',
            'status' => 'active',
        ]);
        $cards->saveOrFail($localCard);

        $foreignCard = $cards->newEntity([
            'novel_id' => 2,
            'card_type' => 'location',
            'name' => 'Citadel',
            'status' => 'active',
        ]);
        $cards->saveOrFail($foreignCard);

        $local = $locations->newEntity(['card_id' => (int)$localCard->id, 'location_type' => 'place']);
        $locations->saveOrFail($local);

        $foreign = $locations->newEntity(['card_id' => (int)$foreignCard->id, 'location_type' => 'structure']);
        $locations->saveOrFail($foreign);

        $service = new LocationService();
        $options = $service->parentOptionsForNovel($this->currentNovelOne());

        $this->assertArrayHasKey((int)$local->id, $options);
        $this->assertArrayNotHasKey((int)$foreign->id, $options);
    }
}
