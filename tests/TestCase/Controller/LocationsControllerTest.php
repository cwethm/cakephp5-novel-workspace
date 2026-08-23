<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

class LocationsControllerTest extends TestCase
{
    use IntegrationTestTrait;

    protected array $fixtures = [
        'app.Users',
        'app.Novels',
        'app.Cards',
        'app.Locations',
    ];

    private function loginAsUserA(): void
    {
        $user = TableRegistry::getTableLocator()->get('Users')->get(1);
        $this->session(['Auth' => $user]);
    }

    public function testAddCreatesCardAndLocationAtomically(): void
    {
        $this->loginAsUserA();

        $cards = TableRegistry::getTableLocator()->get('Cards');
        $locations = TableRegistry::getTableLocator()->get('Locations');

        $beforeCards = $cards->find()->count();
        $beforeLocations = $locations->find()->count();

        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post('/novels/1/locations/add', [
            'card' => [
                'name' => 'Harbor Quarter',
                'short_summary' => 'Busy port district',
                'status' => 'active',
                'novel_id' => 2,
                'card_type' => 'character',
            ],
            'location' => [
                'location_type' => 'settlement',
                'region' => 'North Coast',
            ],
        ]);

        $this->assertResponseSuccess();
        $this->assertRedirectContains('/novels/1/locations/');

        $this->assertSame($beforeCards + 1, $cards->find()->count());
        $this->assertSame($beforeLocations + 1, $locations->find()->count());

        $card = $cards->find()->where(['Cards.name' => 'Harbor Quarter'])->firstOrFail();
        $this->assertSame(1, (int)$card->novel_id);
        $this->assertSame('location', (string)$card->card_type);

        $location = $locations->find()->where(['Locations.card_id' => $card->id])->firstOrFail();
        $this->assertSame('settlement', (string)$location->location_type);
    }

    public function testViewForeignNovelLocationReturns404(): void
    {
        $this->loginAsUserA();

        $cards = TableRegistry::getTableLocator()->get('Cards');
        $locations = TableRegistry::getTableLocator()->get('Locations');

        $card = $cards->newEntity([
            'novel_id' => 2,
            'card_type' => 'location',
            'name' => 'Foreign Place',
            'status' => 'active',
        ]);
        $cards->saveOrFail($card);

        $location = $locations->newEntity([
            'card_id' => (int)$card->id,
            'location_type' => 'place',
        ]);
        $locations->saveOrFail($location);

        $this->get('/novels/1/locations/' . $location->id);

        $this->assertResponseCode(404);
    }

    public function testInitializeCreatesSubtypeForExistingLocationCard(): void
    {
        $this->loginAsUserA();

        $cards = TableRegistry::getTableLocator()->get('Cards');
        $locations = TableRegistry::getTableLocator()->get('Locations');

        $card = $cards->newEntity([
            'novel_id' => 1,
            'card_type' => 'location',
            'name' => 'Init Location Card',
            'status' => 'active',
        ]);
        $cards->saveOrFail($card);

        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post('/novels/1/locations/initialize/' . $card->id, [
            'location' => [
                'location_type' => 'structure',
                'region' => 'Riverfront',
            ],
        ]);

        $this->assertResponseSuccess();
        $this->assertRedirectContains('/novels/1/locations/');
        $this->assertTrue($locations->exists(['card_id' => (int)$card->id]));
    }

    public function testInitializeRejectsNonLocationCard(): void
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

        $this->get('/novels/1/locations/initialize/' . $card->id);

        $this->assertResponseCode(404);
    }

    public function testInitializeRejectsForeignNovelCard(): void
    {
        $this->loginAsUserA();

        $this->get('/novels/1/locations/initialize/2');

        $this->assertResponseCode(404);
    }

    public function testInitializeRejectsCardThatAlreadyHasLocation(): void
    {
        $this->loginAsUserA();

        $cards = TableRegistry::getTableLocator()->get('Cards');
        $locations = TableRegistry::getTableLocator()->get('Locations');

        $card = $cards->newEntity([
            'novel_id' => 1,
            'card_type' => 'location',
            'name' => 'Existing Location',
            'status' => 'active',
        ]);
        $cards->saveOrFail($card);

        $location = $locations->newEntity([
            'card_id' => (int)$card->id,
            'location_type' => 'home',
        ]);
        $locations->saveOrFail($location);

        $this->get('/novels/1/locations/initialize/' . $card->id);

        $this->assertResponseCode(404);
    }

    public function testEditUpdatesCardAndLocation(): void
    {
        $this->loginAsUserA();

        $cards = TableRegistry::getTableLocator()->get('Cards');
        $locations = TableRegistry::getTableLocator()->get('Locations');

        $card = $cards->newEntity([
            'novel_id' => 1,
            'card_type' => 'location',
            'name' => 'Old Plaza',
            'status' => 'active',
        ]);
        $cards->saveOrFail($card);

        $location = $locations->newEntity([
            'card_id' => (int)$card->id,
            'location_type' => 'place',
            'region' => 'Old Region',
        ]);
        $locations->saveOrFail($location);

        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->put('/novels/1/locations/' . $location->id . '/edit', [
            'card' => [
                'name' => 'Old Plaza Updated',
                'status' => 'archived',
            ],
            'location' => [
                'location_type' => 'settlement',
                'region' => 'New Region',
            ],
        ]);

        $this->assertResponseSuccess();
        $this->assertRedirectContains('/novels/1/locations/' . $location->id);

        $updatedCard = $cards->get((int)$card->id);
        $updatedLocation = $locations->get((int)$location->id);

        $this->assertSame('Old Plaza Updated', (string)$updatedCard->name);
        $this->assertSame('archived', (string)$updatedCard->status);
        $this->assertSame('settlement', (string)$updatedLocation->location_type);
        $this->assertSame('New Region', (string)$updatedLocation->region);
    }

    public function testEditRejectsSelfParent(): void
    {
        $this->loginAsUserA();

        $cards = TableRegistry::getTableLocator()->get('Cards');
        $locations = TableRegistry::getTableLocator()->get('Locations');

        $card = $cards->newEntity([
            'novel_id' => 1,
            'card_type' => 'location',
            'name' => 'No Self Parent',
            'status' => 'active',
        ]);
        $cards->saveOrFail($card);

        $location = $locations->newEntity([
            'card_id' => (int)$card->id,
            'location_type' => 'place',
        ]);
        $locations->saveOrFail($location);

        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->put('/novels/1/locations/' . $location->id . '/edit', [
            'card' => [
                'name' => 'No Self Parent Renamed',
                'status' => 'active',
            ],
            'location' => [
                'parent_location_id' => (int)$location->id,
            ],
        ]);

        $this->assertResponseSuccess();

        $unchangedCard = $cards->get((int)$card->id);
        $unchangedLocation = $locations->get((int)$location->id);

        $this->assertSame('No Self Parent', (string)$unchangedCard->name);
        $this->assertNull($unchangedLocation->parent_location_id);
    }

    public function testEditRejectsCycle(): void
    {
        $this->loginAsUserA();

        $cards = TableRegistry::getTableLocator()->get('Cards');
        $locations = TableRegistry::getTableLocator()->get('Locations');

        $rootCard = $cards->newEntity([
            'novel_id' => 1,
            'card_type' => 'location',
            'name' => 'Root',
            'status' => 'active',
        ]);
        $cards->saveOrFail($rootCard);

        $childCard = $cards->newEntity([
            'novel_id' => 1,
            'card_type' => 'location',
            'name' => 'Child',
            'status' => 'active',
        ]);
        $cards->saveOrFail($childCard);

        $root = $locations->newEntity([
            'card_id' => (int)$rootCard->id,
            'location_type' => 'place',
        ]);
        $locations->saveOrFail($root);

        $child = $locations->newEntity([
            'card_id' => (int)$childCard->id,
            'location_type' => 'home',
            'parent_location_id' => (int)$root->id,
        ]);
        $locations->saveOrFail($child);

        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->put('/novels/1/locations/' . $root->id . '/edit', [
            'card' => [
                'name' => 'Root Renamed',
                'status' => 'active',
            ],
            'location' => [
                'parent_location_id' => (int)$child->id,
            ],
        ]);

        $this->assertResponseSuccess();

        $unchangedCard = $cards->get((int)$rootCard->id);
        $unchangedRoot = $locations->get((int)$root->id);

        $this->assertSame('Root', (string)$unchangedCard->name);
        $this->assertNull($unchangedRoot->parent_location_id);
    }

    public function testEditRejectsForeignParentLocation(): void
    {
        $this->loginAsUserA();

        $cards = TableRegistry::getTableLocator()->get('Cards');
        $locations = TableRegistry::getTableLocator()->get('Locations');

        $localCard = $cards->newEntity([
            'novel_id' => 1,
            'card_type' => 'location',
            'name' => 'Local',
            'status' => 'active',
        ]);
        $cards->saveOrFail($localCard);

        $foreignCard = $cards->newEntity([
            'novel_id' => 2,
            'card_type' => 'location',
            'name' => 'Foreign',
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

        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->put('/novels/1/locations/' . $local->id . '/edit', [
            'card' => [
                'name' => 'Local',
                'status' => 'active',
            ],
            'location' => [
                'parent_location_id' => (int)$foreign->id,
            ],
        ]);

        $this->assertResponseCode(404);
    }
}
