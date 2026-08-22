<?php
declare(strict_types=1);

namespace App\Controller;

use App\Domain\Registry\CharacterProfileRegistry;
use App\Service\CharacterService;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;
use Cake\ORM\Exception\PersistenceFailedException;

class CharactersController extends AppController
{
    public function add(int $novel_id): ?Response
    {
        $currentNovel = $this->currentNovel();
        $request = $this->getRequest();
        $cards = $this->fetchTable('Cards');
        $characters = $this->fetchTable('Characters');
        $characterService = new CharacterService();

        $card = $cards->newEmptyEntity();
        $character = $characters->newEmptyEntity();

        if ($request->is('post')) {
            $cardData = (array)$request->getData('card');
            $characterData = (array)$request->getData('character');

            try {
                $created = $characterService->create($currentNovel, $cardData, $characterData);
                $this->Flash->success('Character created.');

                return $this->redirect(['action' => 'view', $currentNovel->id(), $created->id]);
            } catch (PersistenceFailedException $exception) {
                $entity = $exception->getEntity();
                if ($entity->getSource() === 'Cards') {
                    $card = $entity;
                    $character = $characters->newEntity($characterData);
                } else {
                    $card = $cards->newEntity($cardData + ['card_type' => 'character']);
                    $character = $entity;
                }
                $this->Flash->error('Could not save character.');
            }
        }

        $this->set(compact('currentNovel', 'card', 'character'));

        return null;
    }

    public function view(int $novel_id, int $id): void
    {
        $characterService = new CharacterService();
        $character = $characterService->getForNovel($this->currentNovel(), $id);

        $this->set(compact('character'));
    }

    public function edit(int $novel_id, int $id): ?Response
    {
        $currentNovel = $this->currentNovel();
        $request = $this->getRequest();
        $characterService = new CharacterService();
        $character = $characterService->getForNovel($currentNovel, $id);
        $traitTypeOptions = CharacterProfileRegistry::traitTypeOptions();
        $goalTypeOptions = CharacterProfileRegistry::goalTypeOptions();
        $goalStatusOptions = CharacterProfileRegistry::goalStatusOptions();
        if (!$character->has('card')) {
            throw new NotFoundException();
        }

        if ($request->is(['patch', 'post', 'put'])) {
            $cardData = (array)$request->getData('card');
            $characterData = (array)$request->getData('character');
            $appearanceData = (array)$request->getData('appearance');
            $personalityData = (array)$request->getData('personality');
            $voiceData = (array)$request->getData('voice');
            $traitsData = (array)$request->getData('traits', []);
            $skillsData = (array)$request->getData('skills', []);
            $goalsData = (array)$request->getData('goals', []);

            try {
                $character = $characterService->update(
                    $currentNovel,
                    $id,
                    $cardData,
                    $characterData,
                    $appearanceData,
                    $personalityData,
                    $voiceData,
                    $traitsData,
                    $skillsData,
                    $goalsData,
                );
                $this->Flash->success('Character updated.');

                return $this->redirect(['action' => 'view', $currentNovel->id(), $character->id]);
            } catch (PersistenceFailedException) {
                $this->Flash->error('Could not update character.');
                $cards = $this->fetchTable('Cards');
                $characters = $this->fetchTable('Characters');
                $card = $cards->patchEntity($character->card, $cardData);
                $character = $characters->patchEntity($character, $characterData);
                $character->set('card', $card);
                $character->set('character_traits', $traitsData);
                $character->set('character_skills', $skillsData);
                $character->set('character_goals', $goalsData);
            }
        }

        $this->set(compact('currentNovel', 'character', 'traitTypeOptions', 'goalTypeOptions', 'goalStatusOptions'));

        return null;
    }

    public function initializeSubtype(int $novel_id, int $card_id): ?Response
    {
        $currentNovel = $this->currentNovel();
        $request = $this->getRequest();
        $characterService = new CharacterService();
        $card = $characterService->getCharacterCardForNovel($currentNovel, $card_id);
        $characters = $this->fetchTable('Characters');

        if ($characters->exists(['card_id' => $card_id])) {
            throw new NotFoundException();
        }

        $character = $characters->newEmptyEntity();

        if ($request->is('post')) {
            $characterData = (array)$request->getData('character');

            try {
                $character = $characterService->initializeForCard($currentNovel, $card_id, $characterData);
                $this->Flash->success('Character initialized.');

                return $this->redirect(['action' => 'view', $currentNovel->id(), $character->id]);
            } catch (PersistenceFailedException $exception) {
                $character = $exception->getEntity();
                $this->Flash->error('Could not initialize character.');
            }
        }

        $this->set(compact('currentNovel', 'card', 'character'));

        return null;
    }
}
