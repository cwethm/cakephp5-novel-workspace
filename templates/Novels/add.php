<h1>Add Novel</h1>
<?= $this->Form->create($novel) ?>
<?= $this->Form->control('title') ?>
<?= $this->Form->control('subtitle') ?>
<?= $this->Form->control('author_name') ?>
<?= $this->Form->control('description') ?>
<?= $this->Form->control('status', ['options' => [
    'planning' => 'Planning',
    'drafting' => 'Drafting',
    'revising' => 'Revising',
    'complete' => 'Complete',
    'archived' => 'Archived',
]]) ?>
<?= $this->Form->button('Save') ?>
<?= $this->Form->end() ?>
