<?php
/** @var \App\Model\Entity\Card $card */
/** @var array<int, string> $parentOptions */
/** @var array<string, string> $locationTypeOptions */
?>
<h1>Initialize Location</h1>
<p>Card: <?= h($card->name) ?></p>
<?= $this->Form->create(null) ?>
<fieldset>
    <legend>Location Details</legend>
    <?= $this->Form->control('location.parent_location_id', ['options' => $parentOptions, 'empty' => true]) ?>
    <?= $this->Form->control('location.location_type', ['options' => $locationTypeOptions, 'empty' => true]) ?>
    <?= $this->Form->control('location.address') ?>
    <?= $this->Form->control('location.region') ?>
    <?= $this->Form->control('location.country') ?>
    <?= $this->Form->control('location.latitude') ?>
    <?= $this->Form->control('location.longitude') ?>
    <?= $this->Form->control('location.atmosphere') ?>
    <?= $this->Form->control('location.appearance') ?>
    <?= $this->Form->control('location.climate') ?>
    <?= $this->Form->control('location.culture') ?>
    <?= $this->Form->control('location.history') ?>
</fieldset>
<?= $this->Form->button('Initialize Location') ?>
<?= $this->Form->end() ?>
