<?php /** @var \App\Model\Entity\Location $location */ ?>
<h1><?= h($location->card->name ?? 'Location') ?></h1>
<p>
    <?= $this->Html->link('Edit Location', ['action' => 'edit', $location->card->novel_id, $location->id]) ?>
</p>

<h2>Card</h2>
<p><strong>Status:</strong> <?= h((string)($location->card->status ?? '')) ?></p>
<p><strong>Importance:</strong> <?= h((string)($location->card->importance ?? '')) ?></p>
<p><strong>Summary:</strong> <?= h((string)($location->card->short_summary ?? '')) ?></p>
<p><strong>Description:</strong> <?= h((string)($location->card->description ?? '')) ?></p>

<h2>Location Details</h2>
<p><strong>Parent:</strong> <?= h((string)($location->parent_location?->card?->name ?? '')) ?></p>
<p><strong>Type:</strong> <?= h((string)($location->location_type ?? '')) ?></p>
<p><strong>Address:</strong> <?= h((string)($location->address ?? '')) ?></p>
<p><strong>Region:</strong> <?= h((string)($location->region ?? '')) ?></p>
<p><strong>Country:</strong> <?= h((string)($location->country ?? '')) ?></p>
<p><strong>Latitude:</strong> <?= h((string)($location->latitude ?? '')) ?></p>
<p><strong>Longitude:</strong> <?= h((string)($location->longitude ?? '')) ?></p>
<p><strong>Atmosphere:</strong> <?= h((string)($location->atmosphere ?? '')) ?></p>
<p><strong>Appearance:</strong> <?= h((string)($location->appearance ?? '')) ?></p>
<p><strong>Climate:</strong> <?= h((string)($location->climate ?? '')) ?></p>
<p><strong>Culture:</strong> <?= h((string)($location->culture ?? '')) ?></p>
<p><strong>History:</strong> <?= h((string)($location->history ?? '')) ?></p>
