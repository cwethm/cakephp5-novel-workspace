<h1>Edit Location</h1>
<?php /** @var \App\Model\Entity\Location $location */ ?>
<?php /** @var array<int, string> $parentOptions */ ?>
<?php /** @var array<string, string> $locationTypeOptions */ ?>
<?= $this->Form->create(null) ?>
<fieldset>
    <legend>Card</legend>
    <?= $this->Form->control('card.name', ['value' => (string)($location->card->name ?? '')]) ?>
    <?= $this->Form->control('card.short_summary', ['value' => (string)($location->card->short_summary ?? '')]) ?>
    <?= $this->Form->control('card.description', ['value' => (string)($location->card->description ?? '')]) ?>
    <?= $this->Form->control('card.importance', ['value' => (string)($location->card->importance ?? 'normal')]) ?>
    <?= $this->Form->control('card.status', ['options' => ['active' => 'Active', 'archived' => 'Archived'], 'value' => (string)($location->card->status ?? 'active')]) ?>
</fieldset>
<fieldset>
    <legend>Location Details</legend>
    <?= $this->Form->control('location.parent_location_id', ['options' => $parentOptions, 'empty' => true, 'value' => $location->parent_location_id]) ?>
    <?= $this->Form->control('location.location_type', ['options' => $locationTypeOptions, 'empty' => true, 'value' => (string)($location->location_type ?? '')]) ?>
    <?= $this->Form->control('location.address', ['value' => (string)($location->address ?? '')]) ?>
    <?= $this->Form->control('location.region', ['value' => (string)($location->region ?? '')]) ?>
    <?= $this->Form->control('location.country', ['value' => (string)($location->country ?? '')]) ?>
    <?= $this->Form->control('location.latitude', ['value' => (string)($location->latitude ?? '')]) ?>
    <?= $this->Form->control('location.longitude', ['value' => (string)($location->longitude ?? '')]) ?>
    <?= $this->Form->control('location.atmosphere', ['value' => (string)($location->atmosphere ?? '')]) ?>
    <?= $this->Form->control('location.appearance', ['value' => (string)($location->appearance ?? '')]) ?>
    <?= $this->Form->control('location.climate', ['value' => (string)($location->climate ?? '')]) ?>
    <?= $this->Form->control('location.culture', ['value' => (string)($location->culture ?? '')]) ?>
    <?= $this->Form->control('location.history', ['value' => (string)($location->history ?? '')]) ?>
</fieldset>
<?= $this->Form->button('Update Location') ?>
<?= $this->Form->end() ?>
