<h1>Add Character</h1>
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
    <legend>Character Identity</legend>
    <?= $this->Form->control('character.role') ?>
    <?= $this->Form->control('character.aliases') ?>
    <?= $this->Form->control('character.age') ?>
    <?= $this->Form->control('character.birth_date') ?>
    <?= $this->Form->control('character.gender') ?>
    <?= $this->Form->control('character.pronouns') ?>
    <?= $this->Form->control('character.occupation') ?>
    <?= $this->Form->control('character.education') ?>
    <?= $this->Form->control('character.backstory') ?>
    <?= $this->Form->control('character.external_motivation') ?>
    <?= $this->Form->control('character.internal_motivation') ?>
    <?= $this->Form->control('character.core_motivation') ?>
    <?= $this->Form->control('character.central_conflict') ?>
    <?= $this->Form->control('character.family_notes') ?>
    <?= $this->Form->control('character.friendship_notes') ?>
    <?= $this->Form->control('character.culture_notes') ?>
    <?= $this->Form->control('character.religion_notes') ?>
</fieldset>
<?= $this->Form->button('Save Character') ?>
<?= $this->Form->end() ?>
