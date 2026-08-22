<?php
declare(strict_types=1);

namespace App\Controller;

use App\Domain\Registry\CardTypeRegistry;
use App\Service\CardService;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Exception\NotFoundException;

class CardsController extends AppController
{
    public function index(int $novel_id)
    {
        $currentNovel = $this->currentNovel();
        $query = $this->fetchTable('Cards')->find('forNovel', ['novelId' => $currentNovel->id()])
            ->contain(['Tags']);

        $type = $this->request->getQuery('type');
        if (is_string($type) && CardTypeRegistry::has($type)) {
            $query->where(['Cards.card_type' => $type]);
        }

        $status = $this->request->getQuery('status');
        if (is_string($status) && in_array($status, ['active', 'archived'], true)) {
            $query->where(['Cards.status' => $status]);
        }

        $term = trim((string)$this->request->getQuery('q', ''));
        if ($term !== '') {
            $query->where([
                'OR' => [
                    'Cards.name LIKE' => '%' . $term . '%',
                    'Cards.short_summary LIKE' => '%' . $term . '%',
                ],
            ]);
        }

        $tag = $this->request->getQuery('tag');
        if (is_string($tag) && $tag !== '') {
            $query->matching('Tags', function ($q) use ($tag) {
                return $q->where(['Tags.slug' => $tag]);
            });
        }

        $this->set('cards', $this->paginate($query));
        $this->set('novel', $currentNovel->entity());
        $this->set('cardTypes', CardTypeRegistry::all());
    }

    public function add(int $novel_id)
    {
        $currentNovel = $this->currentNovel();
        $cards = $this->fetchTable('Cards');
        $card = $cards->newEmptyEntity();

        if ($this->request->is('post')) {
            $data = $this->request->getData();
            unset($data['novel_id']);
            $card = $cards->patchEntity($card, $data);
            $card->novel_id = $currentNovel->id();

            if ($cards->save($card)) {
                $this->Flash->success('Card created.');

                return $this->redirect(['action' => 'index', $currentNovel->id()]);
            }
            $this->Flash->error('Could not save card.');
        }

        $this->set(compact('card', 'currentNovel'));
        $this->set('cardTypes', CardTypeRegistry::options());
    }

    public function edit(int $novel_id, int $id)
    {
        $currentNovel = $this->currentNovel();
        try {
            $card = $this->fetchTable('Cards')->find('forNovel', ['novelId' => $currentNovel->id()])
                ->where(['Cards.id' => $id])
                ->contain(['Tags'])
                ->firstOrFail();
        } catch (RecordNotFoundException) {
            throw new NotFoundException();
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            unset($data['novel_id'], $data['card_type']);
            $this->fetchTable('Cards')->patchEntity($card, $data);
            if ($this->fetchTable('Cards')->save($card)) {
                $this->Flash->success('Card updated.');

                return $this->redirect(['action' => 'index', $currentNovel->id()]);
            }
            $this->Flash->error('Could not update card.');
        }

        $this->set(compact('card', 'currentNovel'));
    }

    public function archive(int $novel_id, int $id, CardService $cardService)
    {
        $this->request->allowMethod(['post']);
        $currentNovel = $this->currentNovel();

        try {
            $card = $this->fetchTable('Cards')->find('forNovel', ['novelId' => $currentNovel->id()])
                ->where(['Cards.id' => $id])
                ->firstOrFail();
        } catch (RecordNotFoundException) {
            throw new NotFoundException();
        }

        $cardService->archive($currentNovel, $card);
        $this->Flash->success('Card archived.');

        return $this->redirect(['action' => 'index', $currentNovel->id()]);
    }
}
