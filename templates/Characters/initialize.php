<h1>Initialize Character</h1>
<p>Card: <?= h($card->name) ?></p>
<?= $this->Form->create(null) ?>
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
<?= $this->Form->button('Initialize Character') ?>
<?= $this->Form->end() ?>
