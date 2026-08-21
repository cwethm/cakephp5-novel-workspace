<h1>Cards for <?= h($novel->title) ?></h1>
<p><?= $this->Html->link('Add Card', ['action' => 'add', $novel->id]) ?></p>
<?= $this->Form->create(null, ['type' => 'get']) ?>
<?= $this->Form->control('type', ['options' => ['' => 'All'] + $cardTypes, 'label' => 'Type']) ?>
<?= $this->Form->control('status', ['options' => ['' => 'All', 'active' => 'Active', 'archived' => 'Archived']]) ?>
<?= $this->Form->control('tag', ['label' => 'Tag Slug']) ?>
<?= $this->Form->control('q', ['label' => 'Search']) ?>
<?= $this->Form->button('Filter') ?>
<?= $this->Form->end() ?>
<table>
    <thead>
        <tr><th>Name</th><th>Type</th><th>Status</th><th>Slug</th><th></th></tr>
    </thead>
    <tbody>
    <?php foreach ($cards as $card): ?>
        <tr>
            <td><?= h($card->name) ?></td>
            <td><?= h($card->card_type) ?></td>
            <td><?= h($card->status) ?></td>
            <td><?= h($card->slug) ?></td>
            <td>
                <?= $this->Html->link('Edit', ['action' => 'edit', $novel->id, $card->id]) ?>
                <?= $this->Form->postLink('Archive', ['action' => 'archive', $novel->id, $card->id], ['confirm' => 'Archive card?']) ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?= $this->Paginator->numbers() ?>
