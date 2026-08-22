<?php
declare(strict_types=1);

namespace App\Controller;

use App\Domain\CurrentNovel;
use Cake\Controller\Controller;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Exception\NotFoundException;

class AppController extends Controller
{
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('Flash');
        $this->loadComponent('Authentication.Authentication');
    }

    protected function currentUserId(): int
    {
        $identity = $this->request->getAttribute('identity');
        if (!$identity) {
            throw new NotFoundException();
        }

        return (int)$identity->getIdentifier();
    }

    protected function currentNovel(): CurrentNovel
    {
        $novelId = (int)$this->request->getParam('novel_id');
        $userId = $this->currentUserId();

        try {
            $novel = $this->fetchTable('Novels')->find()
                ->where(['Novels.id' => $novelId, 'Novels.user_id' => $userId])
                ->contain(['Users'])
                ->firstOrFail();
        } catch (RecordNotFoundException) {
            throw new NotFoundException();
        }

        return new CurrentNovel($novel, $novel->user ?? null);
    }
}
