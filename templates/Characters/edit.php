<h1>Edit Character</h1>
<?php
/** @var \App\Model\Entity\Character $character */
$appearance = $character->get('character_appearance');
$personality = $character->get('character_personality');
$voice = $character->get('character_voice');
?>
<?= $this->Form->create(null) ?>
<fieldset>
    <legend>Card</legend>
    <?= $this->Form->control('card.name', ['value' => (string)($character->card->name ?? '')]) ?>
    <?= $this->Form->control('card.short_summary', ['value' => (string)($character->card->short_summary ?? '')]) ?>
    <?= $this->Form->control('card.description', ['value' => (string)($character->card->description ?? '')]) ?>
    <?= $this->Form->control('card.importance', ['value' => (string)($character->card->importance ?? 'normal')]) ?>
    <?= $this->Form->control('card.status', ['options' => ['active' => 'Active', 'archived' => 'Archived'], 'value' => (string)($character->card->status ?? 'active')]) ?>
</fieldset>
<fieldset>
    <legend>Character Identity</legend>
    <?= $this->Form->control('character.role', ['value' => (string)($character->role ?? '')]) ?>
    <?= $this->Form->control('character.aliases', ['value' => (string)($character->aliases ?? '')]) ?>
    <?= $this->Form->control('character.age', ['value' => $character->age]) ?>
    <?= $this->Form->control('character.birth_date', ['value' => (string)($character->birth_date ?? '')]) ?>
    <?= $this->Form->control('character.gender', ['value' => (string)($character->gender ?? '')]) ?>
    <?= $this->Form->control('character.pronouns', ['value' => (string)($character->pronouns ?? '')]) ?>
    <?= $this->Form->control('character.occupation', ['value' => (string)($character->occupation ?? '')]) ?>
    <?= $this->Form->control('character.education', ['value' => (string)($character->education ?? '')]) ?>
    <?= $this->Form->control('character.backstory', ['value' => (string)($character->backstory ?? '')]) ?>
    <?= $this->Form->control('character.external_motivation', ['value' => (string)($character->external_motivation ?? '')]) ?>
    <?= $this->Form->control('character.internal_motivation', ['value' => (string)($character->internal_motivation ?? '')]) ?>
    <?= $this->Form->control('character.core_motivation', ['value' => (string)($character->core_motivation ?? '')]) ?>
    <?= $this->Form->control('character.central_conflict', ['value' => (string)($character->central_conflict ?? '')]) ?>
    <?= $this->Form->control('character.family_notes', ['value' => (string)($character->family_notes ?? '')]) ?>
    <?= $this->Form->control('character.friendship_notes', ['value' => (string)($character->friendship_notes ?? '')]) ?>
    <?= $this->Form->control('character.culture_notes', ['value' => (string)($character->culture_notes ?? '')]) ?>
    <?= $this->Form->control('character.religion_notes', ['value' => (string)($character->religion_notes ?? '')]) ?>
</fieldset>
<fieldset>
    <legend>Appearance</legend>
    <?= $this->Form->control('appearance.height', ['value' => (string)($appearance?->height ?? '')]) ?>
    <?= $this->Form->control('appearance.weight', ['value' => (string)($appearance?->weight ?? '')]) ?>
    <?= $this->Form->control('appearance.build', ['value' => (string)($appearance?->build ?? '')]) ?>
    <?= $this->Form->control('appearance.hair_color', ['value' => (string)($appearance?->hair_color ?? '')]) ?>
    <?= $this->Form->control('appearance.hair_style', ['value' => (string)($appearance?->hair_style ?? '')]) ?>
    <?= $this->Form->control('appearance.eye_color', ['value' => (string)($appearance?->eye_color ?? '')]) ?>
    <?= $this->Form->control('appearance.skin_description', ['value' => (string)($appearance?->skin_description ?? '')]) ?>
    <?= $this->Form->control('appearance.facial_features', ['value' => (string)($appearance?->facial_features ?? '')]) ?>
    <?= $this->Form->control('appearance.scars', ['value' => (string)($appearance?->scars ?? '')]) ?>
    <?= $this->Form->control('appearance.clothing_style', ['value' => (string)($appearance?->clothing_style ?? '')]) ?>
    <?= $this->Form->control('appearance.health', ['value' => (string)($appearance?->health ?? '')]) ?>
</fieldset>
<fieldset>
    <legend>Personality</legend>
    <?= $this->Form->control('personality.public_self', ['value' => (string)($personality?->public_self ?? '')]) ?>
    <?= $this->Form->control('personality.private_self', ['value' => (string)($personality?->private_self ?? '')]) ?>
    <?= $this->Form->control('personality.greatest_fear', ['value' => (string)($personality?->greatest_fear ?? '')]) ?>
    <?= $this->Form->control('personality.greatest_desire', ['value' => (string)($personality?->greatest_desire ?? '')]) ?>
    <?= $this->Form->control('personality.wants', ['value' => (string)($personality?->wants ?? '')]) ?>
    <?= $this->Form->control('personality.needs', ['value' => (string)($personality?->needs ?? '')]) ?>
    <?= $this->Form->control('personality.response_to_praise', ['value' => (string)($personality?->response_to_praise ?? '')]) ?>
    <?= $this->Form->control('personality.response_to_conflict', ['value' => (string)($personality?->response_to_conflict ?? '')]) ?>
    <?= $this->Form->control('personality.competitiveness', ['value' => (string)($personality?->competitiveness ?? '')]) ?>
    <?= $this->Form->control('personality.neurotype_notes', ['value' => (string)($personality?->neurotype_notes ?? '')]) ?>
</fieldset>
<fieldset>
    <legend>Voice</legend>
    <?= $this->Form->control('voice.vocabulary_level', ['value' => (string)($voice?->vocabulary_level ?? '')]) ?>
    <?= $this->Form->control('voice.education_level', ['value' => (string)($voice?->education_level ?? '')]) ?>
    <?= $this->Form->control('voice.accent', ['value' => (string)($voice?->accent ?? '')]) ?>
    <?= $this->Form->control('voice.dialect', ['value' => (string)($voice?->dialect ?? '')]) ?>
    <?= $this->Form->control('voice.speaking_style', ['value' => (string)($voice?->speaking_style ?? '')]) ?>
    <?= $this->Form->control('voice.cultural_influences', ['value' => (string)($voice?->cultural_influences ?? '')]) ?>
    <?= $this->Form->control('voice.religious_influences', ['value' => (string)($voice?->religious_influences ?? '')]) ?>
</fieldset>
<?= $this->Form->button('Update Character') ?>
<?= $this->Form->end() ?>
