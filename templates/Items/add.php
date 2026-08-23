<?php
/** @var array<string, string> $itemTypeOptions */
/** @var array<int, string> $ownerCharacterOptions */
/** @var array<int, string> $currentLocationOptions */
?>
<h1>Add Item</h1>
<?= $this->Form->create(null) ?>
<fieldset>
    <legend>Card</legend>
    <?= $this->Form->control('card.name') ?>
    <?= $this->Form->control('card.short_summary') ?>
    <?= $this->Form->control('card.description') ?>
    <?= $this->Form->control('card.importance', ['default' => 'normal']) ?>
    <?= $this->Form->control('card.status', ['options' => ['active' => 'Active', 'archived' => 'Archived'], 'default' => 'active']) ?>
</fieldset>
<fieldset>
    <legend>Item Details</legend>
    <?= $this->Form->control('item.item_type', ['options' => $itemTypeOptions, 'empty' => true]) ?>
    <?= $this->Form->control('item.owner_character_id', ['options' => $ownerCharacterOptions, 'empty' => true]) ?>
    <?= $this->Form->control('item.current_location_id', ['options' => $currentLocationOptions, 'empty' => true]) ?>
    <?= $this->Form->control('item.appearance') ?>
    <?= $this->Form->control('item.history') ?>
    <?= $this->Form->control('item.significance') ?>
    <?= $this->Form->control('item.capabilities') ?>
    <?= $this->Form->control('item.is_unique', ['type' => 'checkbox']) ?>
</fieldset>
<?= $this->Form->button('Save Item') ?>
<?= $this->Form->end() ?>
