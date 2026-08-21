<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Exception\NotFoundException;

class NovelsController extends AppController
{
    public function index()
    {
        $this->set('novels', $this->paginate($this->fetchTable('Novels')->find()->where([
            'Novels.user_id' => $this->currentUserId(),
        ])));
    }

    public function add()
    {
        $novel = $this->fetchTable('Novels')->newEmptyEntity();
        if ($this->request->is('post')) {
            $novel = $this->fetchTable('Novels')->patchEntity($novel, $this->request->getData());
            $novel->user_id = $this->currentUserId();

            if ($this->fetchTable('Novels')->save($novel)) {
                $this->Flash->success('Novel created.');

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error('Could not save novel.');
        }
        $this->set(compact('novel'));
    }

    public function edit(int $id)
    {
        try {
            $novel = $this->fetchTable('Novels')->find()->where([
                'Novels.id' => $id,
                'Novels.user_id' => $this->currentUserId(),
            ])->firstOrFail();
        } catch (RecordNotFoundException) {
            throw new NotFoundException();
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            $novel = $this->fetchTable('Novels')->patchEntity($novel, $this->request->getData());
            if ($this->fetchTable('Novels')->save($novel)) {
                $this->Flash->success('Novel updated.');

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error('Could not update novel.');
        }

        $this->set(compact('novel'));
    }

    public function view(int $id)
    {
        try {
            $novel = $this->fetchTable('Novels')->find()->where([
                'Novels.id' => $id,
                'Novels.user_id' => $this->currentUserId(),
            ])->contain(['Cards' => ['Tags']])->firstOrFail();
        } catch (RecordNotFoundException) {
            throw new NotFoundException();
        }

        $cardsCount = $this->fetchTable('Cards')->find()->where(['novel_id' => $novel->id])->count();
        $tagsCount = $this->fetchTable('Tags')->find()->where(['novel_id' => $novel->id])->count();

        $this->set(compact('novel', 'cardsCount', 'tagsCount'));
    }
}
