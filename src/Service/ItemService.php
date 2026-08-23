<?php
declare(strict_types=1);

namespace App\Service;

use App\Domain\CurrentNovel;
use App\Model\Entity\Card;
use App\Model\Entity\Item;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Exception\NotFoundException;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\ORM\TableRegistry;

class ItemService
{
    /**
     * @param array<string, mixed> $cardData
     * @param array<string, mixed> $itemData
     */
    public function create(CurrentNovel $currentNovel, array $cardData, array $itemData): Item
    {
        $cards = TableRegistry::getTableLocator()->get('Cards');
        $items = TableRegistry::getTableLocator()->get('Items');

        unset($cardData['id'], $cardData['novel_id'], $cardData['card_type']);
        $card = $cards->newEntity($cardData);
        $card->set('novel_id', $currentNovel->id());
        $card->set('card_type', 'item');

        return $cards->getConnection()->transactional(function () use (
            $cards,
            $items,
            $card,
            $itemData,
            $currentNovel,
        ): Item {
            if ($cards->save($card) === false) {
                throw new PersistenceFailedException($card, 'Could not save card');
            }

            unset($itemData['id'], $itemData['card_id']);
            $itemData['owner_character_id'] = $this->normalizeOptionalInt($itemData['owner_character_id'] ?? null);
            $itemData['current_location_id'] = $this->normalizeOptionalInt($itemData['current_location_id'] ?? null);

            /** @var \App\Model\Entity\Item $item */
            $item = $items->newEntity($itemData);
            $item->set('card_id', (int)$card->get('id'));

            $this->assertOwnerCharacterForNovel($currentNovel, $itemData['owner_character_id']);
            $this->assertCurrentLocationForNovel($currentNovel, $itemData['current_location_id']);

            if ($items->save($item) === false) {
                throw new PersistenceFailedException($item, 'Could not save item');
            }

            return $this->getForNovel($currentNovel, (int)$item->get('id'));
        });
    }

    /**
     * @param array<string, mixed> $itemData
     */
    public function initializeForCard(CurrentNovel $currentNovel, int $cardId, array $itemData): Item
    {
        $this->getItemCardForNovel($currentNovel, $cardId);

        $items = TableRegistry::getTableLocator()->get('Items');
        if ($items->exists(['card_id' => $cardId])) {
            throw new NotFoundException();
        }

        unset($itemData['id'], $itemData['card_id']);
        $itemData['owner_character_id'] = $this->normalizeOptionalInt($itemData['owner_character_id'] ?? null);
        $itemData['current_location_id'] = $this->normalizeOptionalInt($itemData['current_location_id'] ?? null);

        /** @var \App\Model\Entity\Item $item */
        $item = $items->newEntity($itemData);
        $item->set('card_id', $cardId);

        $this->assertOwnerCharacterForNovel($currentNovel, $itemData['owner_character_id']);
        $this->assertCurrentLocationForNovel($currentNovel, $itemData['current_location_id']);

        if ($items->save($item) === false) {
            throw new PersistenceFailedException($item, 'Could not save item');
        }

        return $this->getForNovel($currentNovel, (int)$item->get('id'));
    }

    /**
     * @param array<string, mixed> $cardData
     * @param array<string, mixed> $itemData
     */
    public function update(CurrentNovel $currentNovel, int $itemId, array $cardData, array $itemData): Item
    {
        $items = TableRegistry::getTableLocator()->get('Items');
        $cards = TableRegistry::getTableLocator()->get('Cards');

        $item = $this->getForNovel($currentNovel, $itemId);
        if (!$item->card instanceof Card) {
            throw new NotFoundException();
        }

        unset($cardData['id'], $cardData['novel_id'], $cardData['card_type']);
        unset($itemData['id'], $itemData['card_id']);

        $itemData['owner_character_id'] = $this->normalizeOptionalInt($itemData['owner_character_id'] ?? null);
        $itemData['current_location_id'] = $this->normalizeOptionalInt($itemData['current_location_id'] ?? null);

        $cards->patchEntity($item->card, $cardData);
        $items->patchEntity($item, $itemData);

        return $cards->getConnection()->transactional(function () use (
            $cards,
            $items,
            $item,
            $itemData,
            $currentNovel,
        ): Item {
            if ($cards->save($item->card) === false) {
                throw new PersistenceFailedException($item->card, 'Could not save card');
            }

            $this->assertOwnerCharacterForNovel(
                $currentNovel,
                $this->normalizeOptionalInt($itemData['owner_character_id'] ?? null),
            );
            $this->assertCurrentLocationForNovel(
                $currentNovel,
                $this->normalizeOptionalInt($itemData['current_location_id'] ?? null),
            );

            if ($items->save($item) === false) {
                throw new PersistenceFailedException($item, 'Could not save item');
            }

            return $this->getForNovel($currentNovel, (int)$item->id);
        });
    }

    public function getForNovel(CurrentNovel $currentNovel, int $itemId): Item
    {
        $items = TableRegistry::getTableLocator()->get('Items');

        try {
            /** @var \App\Model\Entity\Item $item */
            $item = $items->find()
                ->innerJoinWith('Cards', function ($query) use ($currentNovel) {
                    return $query->where([
                        'Cards.novel_id' => $currentNovel->id(),
                        'Cards.card_type' => 'item',
                    ]);
                })
                ->contain([
                    'Cards',
                    'OwnerCharacters' => ['Cards'],
                    'CurrentLocations' => ['Cards'],
                ])
                ->where(['Items.id' => $itemId])
                ->firstOrFail();
        } catch (RecordNotFoundException) {
            throw new NotFoundException();
        }

        return $item;
    }

    public function getItemCardForNovel(CurrentNovel $currentNovel, int $cardId): Card
    {
        $cards = TableRegistry::getTableLocator()->get('Cards');

        try {
            /** @var \App\Model\Entity\Card $card */
            $card = $cards->find()
                ->where([
                    'Cards.id' => $cardId,
                    'Cards.novel_id' => $currentNovel->id(),
                    'Cards.card_type' => 'item',
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
    public function ownerCharacterOptionsForNovel(CurrentNovel $currentNovel): array
    {
        $characters = TableRegistry::getTableLocator()->get('Characters');
        $query = $characters->find()
            ->innerJoinWith('Cards', function ($q) use ($currentNovel) {
                return $q->where([
                    'Cards.novel_id' => $currentNovel->id(),
                    'Cards.card_type' => 'character',
                ]);
            })
            ->contain(['Cards'])
            ->select(['Characters.id', 'Characters.card_id'])
            ->orderBy(['Cards.name' => 'ASC']);

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

    /**
     * @return array<int, string>
     */
    public function currentLocationOptionsForNovel(CurrentNovel $currentNovel): array
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

    private function assertOwnerCharacterForNovel(CurrentNovel $currentNovel, ?int $characterId): void
    {
        if ($characterId === null) {
            return;
        }

        $characters = TableRegistry::getTableLocator()->get('Characters');

        try {
            $characters->find()
                ->innerJoinWith('Cards', function ($query) use ($currentNovel) {
                    return $query->where([
                        'Cards.novel_id' => $currentNovel->id(),
                        'Cards.card_type' => 'character',
                    ]);
                })
                ->where(['Characters.id' => $characterId])
                ->firstOrFail();
        } catch (RecordNotFoundException) {
            throw new NotFoundException();
        }
    }

    private function assertCurrentLocationForNovel(CurrentNovel $currentNovel, ?int $locationId): void
    {
        if ($locationId === null) {
            return;
        }

        $this->getLocationForNovel($currentNovel, $locationId);
    }

    private function getLocationForNovel(CurrentNovel $currentNovel, int $locationId): void
    {
        $locations = TableRegistry::getTableLocator()->get('Locations');

        try {
            $locations->find()
                ->innerJoinWith('Cards', function ($query) use ($currentNovel) {
                    return $query->where([
                        'Cards.novel_id' => $currentNovel->id(),
                        'Cards.card_type' => 'location',
                    ]);
                })
                ->where(['Locations.id' => $locationId])
                ->firstOrFail();
        } catch (RecordNotFoundException) {
            throw new NotFoundException();
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
