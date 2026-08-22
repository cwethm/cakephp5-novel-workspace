<h1><?= h($character->card->name ?? 'Character') ?></h1>
<p><strong>Role:</strong> <?= h((string)($character->role ?? '')) ?></p>
<p>
    <?= $this->Html->link('Edit Character', ['action' => 'edit', $character->card->novel_id, $character->id]) ?>
</p>

<h2>Card</h2>
<p><strong>Status:</strong> <?= h((string)($character->card->status ?? '')) ?></p>
<p><strong>Importance:</strong> <?= h((string)($character->card->importance ?? '')) ?></p>
<p><strong>Summary:</strong> <?= h((string)($character->card->short_summary ?? '')) ?></p>
<p><strong>Description:</strong> <?= h((string)($character->card->description ?? '')) ?></p>

<h2>Identity Details</h2>
<p><strong>Aliases:</strong> <?= h((string)($character->aliases ?? '')) ?></p>
<p><strong>Age:</strong> <?= h((string)($character->age ?? '')) ?></p>
<p><strong>Birth Date:</strong> <?= h((string)($character->birth_date ?? '')) ?></p>
<p><strong>Gender:</strong> <?= h((string)($character->gender ?? '')) ?></p>
<p><strong>Pronouns:</strong> <?= h((string)($character->pronouns ?? '')) ?></p>
<p><strong>Occupation:</strong> <?= h((string)($character->occupation ?? '')) ?></p>
<p><strong>Education:</strong> <?= h((string)($character->education ?? '')) ?></p>
<p><strong>Backstory:</strong> <?= h((string)($character->backstory ?? '')) ?></p>
<p><strong>External Motivation:</strong> <?= h((string)($character->external_motivation ?? '')) ?></p>
<p><strong>Internal Motivation:</strong> <?= h((string)($character->internal_motivation ?? '')) ?></p>
<p><strong>Core Motivation:</strong> <?= h((string)($character->core_motivation ?? '')) ?></p>
<p><strong>Central Conflict:</strong> <?= h((string)($character->central_conflict ?? '')) ?></p>
<p><strong>Family Notes:</strong> <?= h((string)($character->family_notes ?? '')) ?></p>
<p><strong>Friendship Notes:</strong> <?= h((string)($character->friendship_notes ?? '')) ?></p>
<p><strong>Culture Notes:</strong> <?= h((string)($character->culture_notes ?? '')) ?></p>
<p><strong>Religion Notes:</strong> <?= h((string)($character->religion_notes ?? '')) ?></p>
