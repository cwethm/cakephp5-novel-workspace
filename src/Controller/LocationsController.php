<?php
declare(strict_types=1);

namespace App\Controller;

use App\Domain\Registry\LocationTypeRegistry;
use App\Service\LocationService;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;
use Cake\ORM\Exception\PersistenceFailedException;

class LocationsController extends AppController
{
    public function add(int $novel_id): ?Response
    {
        $currentNovel = $this->currentNovel();
        $request = $this->getRequest();
        $cards = $this->fetchTable('Cards');
        $locations = $this->fetchTable('Locations');
        $locationService = new LocationService();

        $card = $cards->newEmptyEntity();
        $location = $locations->newEmptyEntity();

        if ($request->is('post')) {
            $cardData = (array)$request->getData('card');
            $locationData = (array)$request->getData('location');

            try {
                $created = $locationService->create($currentNovel, $cardData, $locationData);
                $this->Flash->success('Location created.');

                return $this->redirect(['action' => 'view', $currentNovel->id(), $created->id]);
            } catch (PersistenceFailedException $exception) {
                $entity = $exception->getEntity();
                if ($entity->getSource() === 'Cards') {
                    $card = $entity;
                    $location = $locations->newEntity($locationData);
                } else {
                    $card = $cards->newEntity($cardData + ['card_type' => 'location']);
                    $location = $entity;
                }
                $this->Flash->error('Could not save location.');
            }
        }

        $locationTypeOptions = LocationTypeRegistry::options();
        $parentOptions = $locationService->parentOptionsForNovel($currentNovel);
        $this->set(compact('currentNovel', 'card', 'location', 'locationTypeOptions', 'parentOptions'));

        return null;
    }

    public function view(int $novel_id, int $id): void
    {
        $locationService = new LocationService();
        $location = $locationService->getForNovel($this->currentNovel(), $id);

        $this->set(compact('location'));
    }

    public function edit(int $novel_id, int $id): ?Response
    {
        $currentNovel = $this->currentNovel();
        $request = $this->getRequest();
        $locationService = new LocationService();
        $location = $locationService->getForNovel($currentNovel, $id);
        if (!$location->has('card')) {
            throw new NotFoundException();
        }

        if ($request->is(['patch', 'post', 'put'])) {
            $cardData = (array)$request->getData('card');
            $locationData = (array)$request->getData('location');

            try {
                $location = $locationService->update($currentNovel, $id, $cardData, $locationData);
                $this->Flash->success('Location updated.');

                return $this->redirect(['action' => 'view', $currentNovel->id(), $location->id]);
            } catch (PersistenceFailedException) {
                $this->Flash->error('Could not update location.');
                $cards = $this->fetchTable('Cards');
                $locations = $this->fetchTable('Locations');
                $card = $cards->patchEntity($location->card, $cardData);
                $locations->patchEntity($location, $locationData);
                $location->set('card', $card);
            }
        }

        $locationTypeOptions = LocationTypeRegistry::options();
        $parentOptions = $locationService->parentOptionsForNovel($currentNovel, $id);
        $this->set(compact('currentNovel', 'location', 'locationTypeOptions', 'parentOptions'));

        return null;
    }

    public function initializeSubtype(int $novel_id, int $card_id): ?Response
    {
        $currentNovel = $this->currentNovel();
        $request = $this->getRequest();
        $locationService = new LocationService();
        $card = $locationService->getLocationCardForNovel($currentNovel, $card_id);
        $locations = $this->fetchTable('Locations');

        if ($locations->exists(['card_id' => $card_id])) {
            throw new NotFoundException();
        }

        $location = $locations->newEmptyEntity();

        if ($request->is('post')) {
            $locationData = (array)$request->getData('location');

            try {
                $location = $locationService->initializeForCard($currentNovel, $card_id, $locationData);
                $this->Flash->success('Location initialized.');

                return $this->redirect(['action' => 'view', $currentNovel->id(), $location->id]);
            } catch (PersistenceFailedException $exception) {
                $location = $exception->getEntity();
                $this->Flash->error('Could not initialize location.');
            }
        }

        $locationTypeOptions = LocationTypeRegistry::options();
        $parentOptions = $locationService->parentOptionsForNovel($currentNovel);
        $this->set(compact('currentNovel', 'card', 'location', 'locationTypeOptions', 'parentOptions'));

        return null;
    }
}
