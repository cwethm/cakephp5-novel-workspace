<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

class ItemsControllerTest extends TestCase
{
    use IntegrationTestTrait;

    protected array $fixtures = [
        'app.Users',
        'app.Novels',
        'app.Cards',
        'app.Characters',
        'app.Locations',
        'app.Items',
    ];

    private function loginAsUserA(): void
    {
        $user = TableRegistry::getTableLocator()->get('Users')->get(1);
        $this->session(['Auth' => $user]);
    }

    public function testAddCreatesCardAndItemAtomically(): void
    {
        $this->loginAsUserA();

        $cards = TableRegistry::getTableLocator()->get('Cards');
        $items = TableRegistry::getTableLocator()->get('Items');
        $locations = TableRegistry::getTableLocator()->get('Locations');

        $locationCard = $cards->newEntity([
            'novel_id' => 1,
            'card_type' => 'location',
            'name' => 'Storehouse',
            'status' => 'active',
        ]);
        $cards->saveOrFail($locationCard);

        $location = $locations->newEntity([
            'card_id' => (int)$locationCard->id,
            'location_type' => 'structure',
        ]);
        $locations->saveOrFail($location);

        $beforeCards = $cards->find()->count();
        $beforeItems = $items->find()->count();

        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post('/novels/1/items/add', [
            'card' => [
                'name' => 'Iron Key',
                'short_summary' => 'Old gate key',
                'status' => 'active',
                'novel_id' => 2,
                'card_type' => 'character',
            ],
            'item' => [
                'item_type' => 'key_item',
                'owner_character_id' => 1,
                'current_location_id' => (int)$location->id,
                'is_unique' => 1,
            ],
        ]);

        $this->assertResponseSuccess();
        $this->assertRedirectContains('/novels/1/items/');

        $this->assertSame($beforeCards + 1, $cards->find()->count());
        $this->assertSame($beforeItems + 1, $items->find()->count());

        $card = $cards->find()->where(['Cards.name' => 'Iron Key'])->firstOrFail();
        $this->assertSame(1, (int)$card->novel_id);
        $this->assertSame('item', (string)$card->card_type);

        $item = $items->find()->where(['Items.card_id' => $card->id])->firstOrFail();
        $this->assertSame('key_item', (string)$item->item_type);
        $this->assertTrue((bool)$item->is_unique);
    }

    public function testViewForeignNovelItemReturns404(): void
    {
        $this->loginAsUserA();

        $cards = TableRegistry::getTableLocator()->get('Cards');
        $items = TableRegistry::getTableLocator()->get('Items');

        $card = $cards->newEntity([
            'novel_id' => 2,
            'card_type' => 'item',
            'name' => 'Foreign Item',
            'status' => 'active',
        ]);
        $cards->saveOrFail($card);

        $item = $items->newEntity([
            'card_id' => (int)$card->id,
            'item_type' => 'artifact',
        ]);
        $items->saveOrFail($item);

        $this->get('/novels/1/items/' . $item->id);

        $this->assertResponseCode(404);
    }

    public function testInitializeCreatesSubtypeForExistingItemCard(): void
    {
        $this->loginAsUserA();

        $cards = TableRegistry::getTableLocator()->get('Cards');
        $items = TableRegistry::getTableLocator()->get('Items');

        $card = $cards->newEntity([
            'novel_id' => 1,
            'card_type' => 'item',
            'name' => 'Init Item Card',
            'status' => 'active',
        ]);
        $cards->saveOrFail($card);

        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post('/novels/1/items/initialize/' . $card->id, [
            'item' => [
                'item_type' => 'tool',
                'owner_character_id' => 1,
            ],
        ]);

        $this->assertResponseSuccess();
        $this->assertRedirectContains('/novels/1/items/');
        $this->assertTrue($items->exists(['card_id' => (int)$card->id]));
    }

    public function testInitializeRejectsNonItemCard(): void
    {
        $this->loginAsUserA();

        $cards = TableRegistry::getTableLocator()->get('Cards');

        $card = $cards->newEntity([
            'novel_id' => 1,
            'card_type' => 'character',
            'name' => 'Wrong Type',
            'status' => 'active',
        ]);
        $cards->saveOrFail($card);

        $this->get('/novels/1/items/initialize/' . $card->id);

        $this->assertResponseCode(404);
    }

    public function testInitializeRejectsForeignNovelCard(): void
    {
        $this->loginAsUserA();

        $this->get('/novels/1/items/initialize/2');

        $this->assertResponseCode(404);
    }

    public function testInitializeRejectsCardThatAlreadyHasItem(): void
    {
        $this->loginAsUserA();

        $cards = TableRegistry::getTableLocator()->get('Cards');
        $items = TableRegistry::getTableLocator()->get('Items');

        $card = $cards->newEntity([
            'novel_id' => 1,
            'card_type' => 'item',
            'name' => 'Existing Item',
            'status' => 'active',
        ]);
        $cards->saveOrFail($card);

        $item = $items->newEntity([
            'card_id' => (int)$card->id,
            'item_type' => 'tool',
        ]);
        $items->saveOrFail($item);

        $this->get('/novels/1/items/initialize/' . $card->id);

        $this->assertResponseCode(404);
    }

    public function testEditUpdatesCardAndItem(): void
    {
        $this->loginAsUserA();

        $cards = TableRegistry::getTableLocator()->get('Cards');
        $items = TableRegistry::getTableLocator()->get('Items');

        $card = $cards->newEntity([
            'novel_id' => 1,
            'card_type' => 'item',
            'name' => 'Old Trinket',
            'status' => 'active',
        ]);
        $cards->saveOrFail($card);

        $item = $items->newEntity([
            'card_id' => (int)$card->id,
            'item_type' => 'accessory',
            'owner_character_id' => 1,
            'is_unique' => false,
        ]);
        $items->saveOrFail($item);

        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->put('/novels/1/items/' . $item->id . '/edit', [
            'card' => [
                'name' => 'Old Trinket Updated',
                'status' => 'archived',
            ],
            'item' => [
                'item_type' => 'artifact',
                'owner_character_id' => 1,
                'is_unique' => 1,
            ],
        ]);

        $this->assertResponseSuccess();
        $this->assertRedirectContains('/novels/1/items/' . $item->id);

        $updatedCard = $cards->get((int)$card->id);
        $updatedItem = $items->get((int)$item->id);

        $this->assertSame('Old Trinket Updated', (string)$updatedCard->name);
        $this->assertSame('archived', (string)$updatedCard->status);
        $this->assertSame('artifact', (string)$updatedItem->item_type);
        $this->assertTrue((bool)$updatedItem->is_unique);
    }

    public function testEditRejectsForeignOwnerCharacter(): void
    {
        $this->loginAsUserA();

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

        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->put('/novels/1/items/' . $item->id . '/edit', [
            'card' => [
                'name' => 'Local Item',
                'status' => 'active',
            ],
            'item' => [
                'owner_character_id' => 2,
            ],
        ]);

        $this->assertResponseCode(404);
    }

    public function testEditRejectsForeignCurrentLocation(): void
    {
        $this->loginAsUserA();

        $cards = TableRegistry::getTableLocator()->get('Cards');
        $items = TableRegistry::getTableLocator()->get('Items');
        $locations = TableRegistry::getTableLocator()->get('Locations');

        $itemCard = $cards->newEntity([
            'novel_id' => 1,
            'card_type' => 'item',
            'name' => 'Compass',
            'status' => 'active',
        ]);
        $cards->saveOrFail($itemCard);

        $item = $items->newEntity([
            'card_id' => (int)$itemCard->id,
            'item_type' => 'tool',
        ]);
        $items->saveOrFail($item);

        $foreignLocationCard = $cards->newEntity([
            'novel_id' => 2,
            'card_type' => 'location',
            'name' => 'Foreign Vault',
            'status' => 'active',
        ]);
        $cards->saveOrFail($foreignLocationCard);

        $foreignLocation = $locations->newEntity([
            'card_id' => (int)$foreignLocationCard->id,
            'location_type' => 'place',
        ]);
        $locations->saveOrFail($foreignLocation);

        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->put('/novels/1/items/' . $item->id . '/edit', [
            'card' => [
                'name' => 'Compass',
                'status' => 'active',
            ],
            'item' => [
                'current_location_id' => (int)$foreignLocation->id,
            ],
        ]);

        $this->assertResponseCode(404);
    }
}
