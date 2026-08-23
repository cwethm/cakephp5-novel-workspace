<?php
/** @var \App\Model\Entity\Item $item */
/** @var array<string, string> $itemTypeOptions */
/** @var array<int, string> $ownerCharacterOptions */
/** @var array<int, string> $currentLocationOptions */
?>
<h1>Edit Item</h1>
<?= $this->Form->create(null) ?>
<fieldset>
    <legend>Card</legend>
    <?= $this->Form->control('card.name', ['value' => (string)($item->card->name ?? '')]) ?>
    <?= $this->Form->control('card.short_summary', ['value' => (string)($item->card->short_summary ?? '')]) ?>
    <?= $this->Form->control('card.description', ['value' => (string)($item->card->description ?? '')]) ?>
    <?= $this->Form->control('card.importance', ['value' => (string)($item->card->importance ?? 'normal')]) ?>
    <?= $this->Form->control('card.status', ['options' => ['active' => 'Active', 'archived' => 'Archived'], 'value' => (string)($item->card->status ?? 'active')]) ?>
</fieldset>
<fieldset>
    <legend>Item Details</legend>
    <?= $this->Form->control('item.item_type', ['options' => $itemTypeOptions, 'empty' => true, 'value' => (string)($item->item_type ?? '')]) ?>
    <?= $this->Form->control('item.owner_character_id', ['options' => $ownerCharacterOptions, 'empty' => true, 'value' => $item->owner_character_id]) ?>
    <?= $this->Form->control('item.current_location_id', ['options' => $currentLocationOptions, 'empty' => true, 'value' => $item->current_location_id]) ?>
    <?= $this->Form->control('item.appearance', ['value' => (string)($item->appearance ?? '')]) ?>
    <?= $this->Form->control('item.history', ['value' => (string)($item->history ?? '')]) ?>
    <?= $this->Form->control('item.significance', ['value' => (string)($item->significance ?? '')]) ?>
    <?= $this->Form->control('item.capabilities', ['value' => (string)($item->capabilities ?? '')]) ?>
    <?= $this->Form->control('item.is_unique', ['type' => 'checkbox', 'checked' => (bool)($item->is_unique ?? false)]) ?>
</fieldset>
<?= $this->Form->button('Update Item') ?>
<?= $this->Form->end() ?>
