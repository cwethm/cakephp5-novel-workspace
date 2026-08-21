<h1>Novels</h1>
<p><?= $this->Html->link('Add Novel', ['action' => 'add']) ?> | <?= $this->Html->link('Logout', '/logout') ?></p>
<table>
    <thead>
        <tr><th>Title</th><th>Status</th><th>Actions</th></tr>
    </thead>
    <tbody>
        <?php foreach ($novels as $novel): ?>
        <tr>
            <td><?= h($novel->title) ?></td>
            <td><?= h($novel->status) ?></td>
            <td>
                <?= $this->Html->link('Dashboard', ['action' => 'view', $novel->id]) ?>
                <?= $this->Html->link('Edit', ['action' => 'edit', $novel->id]) ?>
                <?= $this->Html->link('Cards', ['controller' => 'Cards', 'action' => 'index', $novel->id]) ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?= $this->Paginator->numbers() ?>
