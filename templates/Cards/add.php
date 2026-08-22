<h1>Add Card</h1>
<?= $this->Form->create($card) ?>
<?= $this->Form->control('name') ?>
<?= $this->Form->control('card_type', ['options' => $cardTypes]) ?>
<?= $this->Form->control('short_summary') ?>
<?= $this->Form->control('description') ?>
<?= $this->Form->control('importance', ['default' => 'normal']) ?>
<?= $this->Form->control('status', ['options' => ['active' => 'Active', 'archived' => 'Archived'], 'default' => 'active']) ?>
<?= $this->Form->button('Save') ?>
<?= $this->Form->end() ?>
