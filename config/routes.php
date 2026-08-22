<?php
declare(strict_types=1);

use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;

return function (RouteBuilder $routes): void {
    $routes->setRouteClass(DashedRoute::class);

    $routes->scope('/', function (RouteBuilder $builder): void {
        $builder->connect('/', ['controller' => 'Novels', 'action' => 'index']);

        $builder->connect('/login', ['controller' => 'Users', 'action' => 'login']);
        $builder->connect('/logout', ['controller' => 'Users', 'action' => 'logout']);

        $builder->connect('/novels', ['controller' => 'Novels', 'action' => 'index']);
        $builder->connect('/novels/add', ['controller' => 'Novels', 'action' => 'add']);
        $builder->connect('/novels/{id}', ['controller' => 'Novels', 'action' => 'view'])
            ->setPass(['id'])
            ->setPatterns(['id' => '\\d+']);
        $builder->connect('/novels/{id}/edit', ['controller' => 'Novels', 'action' => 'edit'])
            ->setPass(['id'])
            ->setPatterns(['id' => '\\d+']);

        $builder->connect('/novels/{novel_id}/cards', ['controller' => 'Cards', 'action' => 'index'])
            ->setPass(['novel_id'])
            ->setPatterns(['novel_id' => '\\d+']);
        $builder->connect('/novels/{novel_id}/cards/add', ['controller' => 'Cards', 'action' => 'add'])
            ->setPass(['novel_id'])
            ->setPatterns(['novel_id' => '\\d+']);
        $builder->connect('/novels/{novel_id}/cards/{id}/edit', ['controller' => 'Cards', 'action' => 'edit'])
            ->setPass(['novel_id', 'id'])
            ->setPatterns(['novel_id' => '\\d+', 'id' => '\\d+']);
        $builder->connect('/novels/{novel_id}/cards/{id}/archive', ['controller' => 'Cards', 'action' => 'archive'])
            ->setPass(['novel_id', 'id'])
            ->setPatterns(['novel_id' => '\\d+', 'id' => '\\d+']);

        $builder->connect('/novels/{novel_id}/characters/add', ['controller' => 'Characters', 'action' => 'add'])
            ->setPass(['novel_id'])
            ->setPatterns(['novel_id' => '\\d+']);
        $builder->connect('/novels/{novel_id}/characters/initialize/{card_id}', ['controller' => 'Characters', 'action' => 'initializeSubtype'])
            ->setPass(['novel_id', 'card_id'])
            ->setPatterns(['novel_id' => '\\d+', 'card_id' => '\\d+']);
        $builder->connect('/novels/{novel_id}/characters/{id}', ['controller' => 'Characters', 'action' => 'view'])
            ->setPass(['novel_id', 'id'])
            ->setPatterns(['novel_id' => '\\d+', 'id' => '\\d+']);
        $builder->connect('/novels/{novel_id}/characters/{id}/edit', ['controller' => 'Characters', 'action' => 'edit'])
            ->setPass(['novel_id', 'id'])
            ->setPatterns(['novel_id' => '\\d+', 'id' => '\\d+']);

        $builder->fallbacks();
    });
};
