<?php
declare(strict_types=1);

namespace App\Test\TestCase\Service;

use App\Domain\CurrentNovel;
use App\Service\ItemService;
use Cake\Http\Exception\NotFoundException;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

class ItemServiceTest extends TestCase
{
    protected array $fixtures = [
        'app.Users',
        'app.Novels',
        'app.Cards',
        'app.Characters',
        'app.Locations',
        'app.Items',
    ];

    private function currentNovelOne(): CurrentNovel
    {
        $novels = TableRegistry::getTableLocator()->get('Novels');
        $users = TableRegistry::getTableLocator()->get('Users');

        return new CurrentNovel($novels->get(1), $users->get(1));
    }

    public function testCreatePersistsCardAndItem(): void
    {
        $cards = TableRegistry::getTableLocator()->get('Cards');
        $locations = TableRegistry::getTableLocator()->get('Locations');

        $locationCard = $cards->newEntity([
            'novel_id' => 1,
            'card_type' => 'location',
            'name' => 'Warehouse',
            'status' => 'active',
        ]);
        $cards->saveOrFail($locationCard);

        $location = $locations->newEntity([
            'card_id' => (int)$locationCard->id,
            'location_type' => 'structure',
        ]);
        $locations->saveOrFail($location);

        $service = new ItemService();
        $created = $service->create(
            $this->currentNovelOne(),
            [
                'name' => 'New Item Card',
                'status' => 'active',
                'novel_id' => 2,
                'card_type' => 'character',
            ],
            [
                'item_type' => 'tool',
                'owner_character_id' => 1,
                'current_location_id' => (int)$location->id,
                'is_unique' => true,
            ],
        );

        $items = TableRegistry::getTableLocator()->get('Items');

        $card = $cards->get((int)$created->card_id);
        $this->assertSame(1, (int)$card->novel_id);
        $this->assertSame('item', (string)$card->card_type);
        $this->assertTrue($items->exists(['id' => (int)$created->id]));
        $this->assertSame('tool', (string)$created->item_type);
        $this->assertTrue((bool)$created->is_unique);
    }

    public function testCreateIsAtomicWhenItemValidationFails(): void
    {
        $service = new ItemService();
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
                    'item_type' => 'unknown_type',
                ],
            );
            $this->fail('Expected PersistenceFailedException was not thrown.');
        } catch (PersistenceFailedException) {
            $this->assertSame($before, $cards->find()->count());
        }
    }

    public function testCreateRejectsForeignOwnerCharacter(): void
    {
        $service = new ItemService();

        $this->expectException(NotFoundException::class);
        $service->create(
            $this->currentNovelOne(),
            [
                'name' => 'Foreign Owner Card',
                'status' => 'active',
            ],
            [
                'item_type' => 'weapon',
                'owner_character_id' => 2,
            ],
        );
    }

    public function testCreateRejectsForeignCurrentLocation(): void
    {
        $cards = TableRegistry::getTableLocator()->get('Cards');
        $locations = TableRegistry::getTableLocator()->get('Locations');

        $foreignLocationCard = $cards->newEntity([
            'novel_id' => 2,
            'card_type' => 'location',
            'name' => 'Foreign Location',
            'status' => 'active',
        ]);
        $cards->saveOrFail($foreignLocationCard);

        $foreignLocation = $locations->newEntity([
            'card_id' => (int)$foreignLocationCard->id,
            'location_type' => 'place',
        ]);
        $locations->saveOrFail($foreignLocation);

        $service = new ItemService();

        $this->expectException(NotFoundException::class);
        $service->create(
            $this->currentNovelOne(),
            [
                'name' => 'Foreign Location Card',
                'status' => 'active',
            ],
            [
                'item_type' => 'artifact',
                'current_location_id' => (int)$foreignLocation->id,
            ],
        );
    }

    public function testInitializeRejectsForeignNovelCard(): void
    {
        $service = new ItemService();

        $this->expectException(NotFoundException::class);
        $service->initializeForCard($this->currentNovelOne(), 2, ['item_type' => 'tool']);
    }

    public function testInitializeRejectsNonItemCard(): void
    {
        $cards = TableRegistry::getTableLocator()->get('Cards');

        $card = $cards->newEntity([
            'novel_id' => 1,
            'card_type' => 'character',
            'name' => 'Wrong Type',
            'status' => 'active',
        ]);
        $cards->saveOrFail($card);

        $service = new ItemService();
        $this->expectException(NotFoundException::class);
        $service->initializeForCard($this->currentNovelOne(), (int)$card->id, ['item_type' => 'tool']);
    }

    public function testInitializeRejectsDuplicateItemPerCard(): void
    {
        $cards = TableRegistry::getTableLocator()->get('Cards');
        $items = TableRegistry::getTableLocator()->get('Items');

        $card = $cards->newEntity([
            'novel_id' => 1,
            'card_type' => 'item',
            'name' => 'Already Initialized',
            'status' => 'active',
        ]);
        $cards->saveOrFail($card);

        $item = $items->newEntity([
            'card_id' => (int)$card->id,
            'item_type' => 'tool',
        ]);
        $items->saveOrFail($item);

        $service = new ItemService();
        $this->expectException(NotFoundException::class);
        $service->initializeForCard($this->currentNovelOne(), (int)$card->id, ['item_type' => 'document']);
    }

    public function testUpdateRejectsForeignOwnerCharacter(): void
    {
        $cards = TableRegistry::getTableLocator()->get('Cards');
        $items = TableRegistry::getTableLocator()->get('Items');

        $card = $cards->newEntity([
            'novel_id' => 1,
            'card_type' => 'item',
            'name' => 'Local Item',
            'status' => 'active',
        ]);
        $cards->saveOrFail($card);

        $item = $items->newEntity([
            'card_id' => (int)$card->id,
            'item_type' => 'tool',
            'owner_character_id' => 1,
        ]);
        $items->saveOrFail($item);

        $service = new ItemService();

        $this->expectException(NotFoundException::class);
        $service->update(
            $this->currentNovelOne(),
            (int)$item->id,
            ['name' => 'Local Item'],
            ['owner_character_id' => 2],
        );
    }

    public function testUpdateRejectsForeignCurrentLocation(): void
    {
        $cards = TableRegistry::getTableLocator()->get('Cards');
        $items = TableRegistry::getTableLocator()->get('Items');
        $locations = TableRegistry::getTableLocator()->get('Locations');

        $localItemCard = $cards->newEntity([
            'novel_id' => 1,
            'card_type' => 'item',
            'name' => 'Local Item',
            'status' => 'active',
        ]);
        $cards->saveOrFail($localItemCard);

        $item = $items->newEntity([
            'card_id' => (int)$localItemCard->id,
            'item_type' => 'artifact',
        ]);
        $items->saveOrFail($item);

        $foreignLocationCard = $cards->newEntity([
            'novel_id' => 2,
            'card_type' => 'location',
            'name' => 'Foreign Location',
            'status' => 'active',
        ]);
        $cards->saveOrFail($foreignLocationCard);

        $foreignLocation = $locations->newEntity([
            'card_id' => (int)$foreignLocationCard->id,
            'location_type' => 'place',
        ]);
        $locations->saveOrFail($foreignLocation);

        $service = new ItemService();

        $this->expectException(NotFoundException::class);
        $service->update(
            $this->currentNovelOne(),
            (int)$item->id,
            ['name' => 'Local Item'],
            ['current_location_id' => (int)$foreignLocation->id],
        );
    }

    public function testOwnerCharacterOptionsStayNovelScoped(): void
    {
        $cards = TableRegistry::getTableLocator()->get('Cards');
        $characters = TableRegistry::getTableLocator()->get('Characters');

        $localCharacterCard = $cards->newEntity([
            'novel_id' => 1,
            'card_type' => 'character',
            'name' => 'Local Character',
            'status' => 'active',
        ]);
        $cards->saveOrFail($localCharacterCard);

        $localCharacter = $characters->newEntity([
            'card_id' => (int)$localCharacterCard->id,
            'role' => 'supporting',
        ]);
        $characters->saveOrFail($localCharacter);

        $foreignCharacterCard = $cards->newEntity([
            'novel_id' => 2,
            'card_type' => 'character',
            'name' => 'Foreign Character',
            'status' => 'active',
        ]);
        $cards->saveOrFail($foreignCharacterCard);

        $foreignCharacter = $characters->newEntity([
            'card_id' => (int)$foreignCharacterCard->id,
            'role' => 'supporting',
        ]);
        $characters->saveOrFail($foreignCharacter);

        $service = new ItemService();
        $options = $service->ownerCharacterOptionsForNovel($this->currentNovelOne());

        $this->assertArrayHasKey((int)$localCharacter->id, $options);
        $this->assertArrayNotHasKey((int)$foreignCharacter->id, $options);
    }

    public function testCurrentLocationOptionsStayNovelScoped(): void
    {
        $cards = TableRegistry::getTableLocator()->get('Cards');
        $locations = TableRegistry::getTableLocator()->get('Locations');

        $localLocationCard = $cards->newEntity([
            'novel_id' => 1,
            'card_type' => 'location',
            'name' => 'Local Location',
            'status' => 'active',
        ]);
        $cards->saveOrFail($localLocationCard);

        $localLocation = $locations->newEntity([
            'card_id' => (int)$localLocationCard->id,
            'location_type' => 'place',
        ]);
        $locations->saveOrFail($localLocation);

        $foreignLocationCard = $cards->newEntity([
            'novel_id' => 2,
            'card_type' => 'location',
            'name' => 'Foreign Location',
            'status' => 'active',
        ]);
        $cards->saveOrFail($foreignLocationCard);

        $foreignLocation = $locations->newEntity([
            'card_id' => (int)$foreignLocationCard->id,
            'location_type' => 'place',
        ]);
        $locations->saveOrFail($foreignLocation);

        $service = new ItemService();
        $options = $service->currentLocationOptionsForNovel($this->currentNovelOne());

        $this->assertArrayHasKey((int)$localLocation->id, $options);
        $this->assertArrayNotHasKey((int)$foreignLocation->id, $options);
    }
}
