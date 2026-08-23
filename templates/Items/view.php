<?php /** @var \App\Model\Entity\Item $item */ ?>
<h1><?= h($item->card->name ?? 'Item') ?></h1>
<p>
    <?= $this->Html->link('Edit Item', ['action' => 'edit', $item->card->novel_id, $item->id]) ?>
</p>

<h2>Card</h2>
<p><strong>Status:</strong> <?= h((string)($item->card->status ?? '')) ?></p>
<p><strong>Importance:</strong> <?= h((string)($item->card->importance ?? '')) ?></p>
<p><strong>Summary:</strong> <?= h((string)($item->card->short_summary ?? '')) ?></p>
<p><strong>Description:</strong> <?= h((string)($item->card->description ?? '')) ?></p>

<h2>Item Details</h2>
<p><strong>Type:</strong> <?= h((string)($item->item_type ?? '')) ?></p>
<p><strong>Owner Character:</strong> <?= h((string)($item->owner_character?->card?->name ?? '')) ?></p>
<p><strong>Current Location:</strong> <?= h((string)($item->current_location?->card?->name ?? '')) ?></p>
<p><strong>Appearance:</strong> <?= h((string)($item->appearance ?? '')) ?></p>
<p><strong>History:</strong> <?= h((string)($item->history ?? '')) ?></p>
<p><strong>Significance:</strong> <?= h((string)($item->significance ?? '')) ?></p>
<p><strong>Capabilities:</strong> <?= h((string)($item->capabilities ?? '')) ?></p>
<p><strong>Unique:</strong> <?= h((bool)($item->is_unique ?? false) ? 'Yes' : 'No') ?></p>
