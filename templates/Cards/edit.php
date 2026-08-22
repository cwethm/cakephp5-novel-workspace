<h1>Edit Card</h1>
<?= $this->Form->create($card) ?>
<?= $this->Form->control('name') ?>
<?= $this->Form->control('short_summary') ?>
<?= $this->Form->control('description') ?>
<?= $this->Form->control('importance') ?>
<?= $this->Form->control('status', ['options' => ['active' => 'Active', 'archived' => 'Archived']]) ?>
<?= $this->Form->button('Update') ?>
<?= $this->Form->end() ?>
