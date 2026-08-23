<?php
declare(strict_types=1);

namespace App\Controller;

use App\Domain\Registry\ItemTypeRegistry;
use App\Service\ItemService;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;
use Cake\ORM\Exception\PersistenceFailedException;

class ItemsController extends AppController
{
    public function add(int $novel_id): ?Response
    {
        $currentNovel = $this->currentNovel();
        $request = $this->getRequest();
        $cards = $this->fetchTable('Cards');
        $items = $this->fetchTable('Items');
        $itemService = new ItemService();

        $card = $cards->newEmptyEntity();
        $item = $items->newEmptyEntity();

        if ($request->is('post')) {
            $cardData = (array)$request->getData('card');
            $itemData = (array)$request->getData('item');

            try {
                $created = $itemService->create($currentNovel, $cardData, $itemData);
                $this->Flash->success('Item created.');

                return $this->redirect(['action' => 'view', $currentNovel->id(), $created->id]);
            } catch (PersistenceFailedException $exception) {
                $entity = $exception->getEntity();
                if ($entity->getSource() === 'Cards') {
                    $card = $entity;
                    $item = $items->newEntity($itemData);
                } else {
                    $card = $cards->newEntity($cardData + ['card_type' => 'item']);
                    $item = $entity;
                }
                $this->Flash->error('Could not save item.');
            }
        }

        $itemTypeOptions = ItemTypeRegistry::options();
        $ownerCharacterOptions = $itemService->ownerCharacterOptionsForNovel($currentNovel);
        $currentLocationOptions = $itemService->currentLocationOptionsForNovel($currentNovel);
        $this->set(compact(
            'currentNovel',
            'card',
            'item',
            'itemTypeOptions',
            'ownerCharacterOptions',
            'currentLocationOptions',
        ));

        return null;
    }

    public function view(int $novel_id, int $id): void
    {
        $itemService = new ItemService();
        $item = $itemService->getForNovel($this->currentNovel(), $id);

        $this->set(compact('item'));
    }

    public function edit(int $novel_id, int $id): ?Response
    {
        $currentNovel = $this->currentNovel();
        $request = $this->getRequest();
        $itemService = new ItemService();
        $item = $itemService->getForNovel($currentNovel, $id);
        if (!$item->has('card')) {
            throw new NotFoundException();
        }

        if ($request->is(['patch', 'post', 'put'])) {
            $cardData = (array)$request->getData('card');
            $itemData = (array)$request->getData('item');

            try {
                $item = $itemService->update($currentNovel, $id, $cardData, $itemData);
                $this->Flash->success('Item updated.');

                return $this->redirect(['action' => 'view', $currentNovel->id(), $item->id]);
            } catch (PersistenceFailedException) {
                $this->Flash->error('Could not update item.');
                $cards = $this->fetchTable('Cards');
                $items = $this->fetchTable('Items');
                $card = $cards->patchEntity($item->card, $cardData);
                $items->patchEntity($item, $itemData);
                $item->set('card', $card);
            }
        }

        $itemTypeOptions = ItemTypeRegistry::options();
        $ownerCharacterOptions = $itemService->ownerCharacterOptionsForNovel($currentNovel);
        $currentLocationOptions = $itemService->currentLocationOptionsForNovel($currentNovel);
        $this->set(compact(
            'currentNovel',
            'item',
            'itemTypeOptions',
            'ownerCharacterOptions',
            'currentLocationOptions',
        ));

        return null;
    }

    public function initializeSubtype(int $novel_id, int $card_id): ?Response
    {
        $currentNovel = $this->currentNovel();
        $request = $this->getRequest();
        $itemService = new ItemService();
        $card = $itemService->getItemCardForNovel($currentNovel, $card_id);
        $items = $this->fetchTable('Items');

        if ($items->exists(['card_id' => $card_id])) {
            throw new NotFoundException();
        }

        $item = $items->newEmptyEntity();

        if ($request->is('post')) {
            $itemData = (array)$request->getData('item');

            try {
                $item = $itemService->initializeForCard($currentNovel, $card_id, $itemData);
                $this->Flash->success('Item initialized.');

                return $this->redirect(['action' => 'view', $currentNovel->id(), $item->id]);
            } catch (PersistenceFailedException $exception) {
                $item = $exception->getEntity();
                $this->Flash->error('Could not initialize item.');
            }
        }

        $itemTypeOptions = ItemTypeRegistry::options();
        $ownerCharacterOptions = $itemService->ownerCharacterOptionsForNovel($currentNovel);
        $currentLocationOptions = $itemService->currentLocationOptionsForNovel($currentNovel);
        $this->set(compact(
            'currentNovel',
            'card',
            'item',
            'itemTypeOptions',
            'ownerCharacterOptions',
            'currentLocationOptions',
        ));

        return null;
    }
}
