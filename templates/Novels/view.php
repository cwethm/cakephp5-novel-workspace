<h1><?= h($novel->title) ?></h1>
<p>Status: <?= h($novel->status) ?></p>
<p><?= h($novel->description) ?></p>
<ul>
    <li>Cards: <?= (int)$cardsCount ?></li>
    <li>Tags: <?= (int)$tagsCount ?></li>
</ul>
<p><?= $this->Html->link('Browse Cards', ['controller' => 'Cards', 'action' => 'index', $novel->id]) ?></p>
