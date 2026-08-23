<?php
/** @var \App\Model\Entity\Card $card */
/** @var array<string, string> $itemTypeOptions */
/** @var array<int, string> $ownerCharacterOptions */
/** @var array<int, string> $currentLocationOptions */
?>
<h1>Initialize Item</h1>
<p>Card: <?= h($card->name) ?></p>
<?= $this->Form->create(null) ?>
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
<?= $this->Form->button('Initialize Item') ?>
<?= $this->Form->end() ?>
