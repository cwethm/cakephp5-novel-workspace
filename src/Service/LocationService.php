<?php
declare(strict_types=1);

namespace App\Service;

use App\Domain\CurrentNovel;
use App\Model\Entity\Card;
use App\Model\Entity\Location;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Exception\NotFoundException;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\ORM\TableRegistry;

class LocationService
{
    /**
     * @param array<string, mixed> $cardData
     * @param array<string, mixed> $locationData
     */
    public function create(CurrentNovel $currentNovel, array $cardData, array $locationData): Location
    {
        $cards = TableRegistry::getTableLocator()->get('Cards');
        $locations = TableRegistry::getTableLocator()->get('Locations');

        unset($cardData['id'], $cardData['novel_id'], $cardData['card_type']);
        $card = $cards->newEntity($cardData);
        $card->set('novel_id', $currentNovel->id());
        $card->set('card_type', 'location');

        return $cards->getConnection()->transactional(function () use (
            $cards,
            $locations,
            $card,
            $locationData,
            $currentNovel,
        ): Location {
            if ($cards->save($card) === false) {
                throw new PersistenceFailedException($card, 'Could not save card');
            }

            unset($locationData['id'], $locationData['card_id']);
            $locationData['parent_location_id'] = $this->normalizeOptionalInt(
                $locationData['parent_location_id'] ?? null,
            );

            /** @var \App\Model\Entity\Location $location */
            $location = $locations->newEntity($locationData);
            $location->set('card_id', (int)$card->get('id'));

            $this->assertValidParent($currentNovel, null, $location, $locationData['parent_location_id']);

            if ($locations->save($location) === false) {
                throw new PersistenceFailedException($location, 'Could not save location');
            }

            return $this->getForNovel($currentNovel, (int)$location->get('id'));
        });
    }

    /**
     * @param array<string, mixed> $locationData
     */
    public function initializeForCard(CurrentNovel $currentNovel, int $cardId, array $locationData): Location
    {
        $this->getLocationCardForNovel($currentNovel, $cardId);

        $locations = TableRegistry::getTableLocator()->get('Locations');
        if ($locations->exists(['card_id' => $cardId])) {
            throw new NotFoundException();
        }

        unset($locationData['id'], $locationData['card_id']);
        $locationData['parent_location_id'] = $this->normalizeOptionalInt($locationData['parent_location_id'] ?? null);

        /** @var \App\Model\Entity\Location $location */
        $location = $locations->newEntity($locationData);
        $location->set('card_id', $cardId);

        $this->assertValidParent($currentNovel, null, $location, $locationData['parent_location_id']);

        if ($locations->save($location) === false) {
            throw new PersistenceFailedException($location, 'Could not save location');
        }

        return $this->getForNovel($currentNovel, (int)$location->get('id'));
    }

    /**
     * @param array<string, mixed> $cardData
     * @param array<string, mixed> $locationData
     */
    public function update(CurrentNovel $currentNovel, int $locationId, array $cardData, array $locationData): Location
    {
        $locations = TableRegistry::getTableLocator()->get('Locations');
        $cards = TableRegistry::getTableLocator()->get('Cards');

        $location = $this->getForNovel($currentNovel, $locationId);
        if (!$location->card instanceof Card) {
            throw new NotFoundException();
        }

        unset($cardData['id'], $cardData['novel_id'], $cardData['card_type']);
        unset($locationData['id'], $locationData['card_id']);

        $locationData['parent_location_id'] = $this->normalizeOptionalInt($locationData['parent_location_id'] ?? null);

        $cards->patchEntity($location->card, $cardData);
        $locations->patchEntity($location, $locationData);

        return $cards->getConnection()->transactional(function () use (
            $cards,
            $locations,
            $location,
            $locationData,
            $currentNovel,
        ): Location {
            if ($cards->save($location->card) === false) {
                throw new PersistenceFailedException($location->card, 'Could not save card');
            }

            $this->assertValidParent(
                $currentNovel,
                (int)$location->id,
                $location,
                $this->normalizeOptionalInt($locationData['parent_location_id'] ?? null),
            );

            if ($locations->save($location) === false) {
                throw new PersistenceFailedException($location, 'Could not save location');
            }

            return $this->getForNovel($currentNovel, (int)$location->id);
        });
    }

    public function getForNovel(CurrentNovel $currentNovel, int $locationId): Location
    {
        $locations = TableRegistry::getTableLocator()->get('Locations');

        try {
            /** @var \App\Model\Entity\Location $location */
            $location = $locations->find()
                ->innerJoinWith('Cards', function ($query) use ($currentNovel) {
                    return $query->where([
                        'Cards.novel_id' => $currentNovel->id(),
                        'Cards.card_type' => 'location',
                    ]);
                })
                ->contain(['Cards', 'ParentLocations' => ['Cards']])
                ->where(['Locations.id' => $locationId])
                ->firstOrFail();
        } catch (RecordNotFoundException) {
            throw new NotFoundException();
        }

        return $location;
    }

    public function getLocationCardForNovel(CurrentNovel $currentNovel, int $cardId): Card
    {
        $cards = TableRegistry::getTableLocator()->get('Cards');

        try {
            /** @var \App\Model\Entity\Card $card */
            $card = $cards->find()
                ->where([
                    'Cards.id' => $cardId,
                    'Cards.novel_id' => $currentNovel->id(),
                    'Cards.card_type' => 'location',
                ])
                ->firstOrFail();
        } catch (RecordNotFoundException) {
            throw new NotFoundException();
        }

        return $card;
    }

    /**
     * @return array<int, string>
     */
    public function parentOptionsForNovel(CurrentNovel $currentNovel, ?int $excludeLocationId = null): array
    {
        $locations = TableRegistry::getTableLocator()->get('Locations');
        $query = $locations->find()
            ->innerJoinWith('Cards', function ($q) use ($currentNovel) {
                return $q->where([
                    'Cards.novel_id' => $currentNovel->id(),
                    'Cards.card_type' => 'location',
                ]);
            })
            ->contain(['Cards'])
            ->select(['Locations.id', 'Locations.card_id'])
            ->orderBy(['Cards.name' => 'ASC']);

        if ($excludeLocationId !== null) {
            $query->where(['Locations.id !=' => $excludeLocationId]);
        }

        $options = [];
        foreach ($query->all() as $row) {
            $card = $row->get('card');
            if (!$card instanceof Card) {
                continue;
            }
            $options[(int)$row->get('id')] = (string)$card->name;
        }

        return $options;
    }

    private function assertValidParent(
        CurrentNovel $currentNovel,
        ?int $locationId,
        Location $location,
        ?int $parentLocationId,
    ): void {
        if ($parentLocationId === null) {
            return;
        }

        if ($locationId !== null && $parentLocationId === $locationId) {
            $location->setError('parent_location_id', ['selfParent' => 'Location cannot be its own parent']);
            throw new PersistenceFailedException($location, 'Location cannot be its own parent');
        }

        $parent = $this->getForNovel($currentNovel, $parentLocationId);

        if ($locationId === null) {
            return;
        }

        $seen = [];
        while ($parent->parent_location_id !== null) {
            $ancestorId = (int)$parent->parent_location_id;
            if ($ancestorId === $locationId) {
                $location->setError('parent_location_id', ['cycle' => 'Parent location would create a cycle']);
                throw new PersistenceFailedException($location, 'Parent location would create a cycle');
            }
            if (isset($seen[$ancestorId])) {
                break;
            }
            $seen[$ancestorId] = true;
            $parent = $this->getForNovel($currentNovel, $ancestorId);
        }
    }

    private function normalizeOptionalInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int)$value;
    }
}
