<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;

class UsersController extends AppController
{
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);
        $this->Authentication->addUnauthenticatedActions(['login']);
    }

    public function login(): ?Response
    {
        $this->request->allowMethod(['get', 'post']);
        $result = $this->Authentication->getResult();

        if ($result->isValid()) {
            $redirect = $this->Authentication->getLoginRedirect('/novels');

            return $this->redirect($redirect);
        }

        if ($this->request->is('post')) {
            $this->Flash->error('Invalid email or password.');
        }

        return null;
    }

    public function logout(): ?Response
    {
        $result = $this->Authentication->getResult();
        if (!$result->isValid()) {
            throw new NotFoundException();
        }

        $this->Authentication->logout();
        $this->Flash->success('You are now logged out.');

        return $this->redirect('/login');
    }
}
