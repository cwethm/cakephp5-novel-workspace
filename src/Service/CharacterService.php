<?php
declare(strict_types=1);

namespace App\Service;

use App\Domain\CurrentNovel;
use App\Model\Entity\Card;
use App\Model\Entity\Character;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Exception\NotFoundException;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;

class CharacterService
{
    /**
     * @param array<string, mixed> $cardData
     * @param array<string, mixed> $characterData
     */
    public function create(CurrentNovel $currentNovel, array $cardData, array $characterData): Character
    {
        $cards = TableRegistry::getTableLocator()->get('Cards');
        $characters = TableRegistry::getTableLocator()->get('Characters');

        unset($cardData['id'], $cardData['novel_id'], $cardData['card_type']);

        /** @var \App\Model\Entity\Card $card */
        $card = $cards->newEntity($cardData);
        $card->set('novel_id', $currentNovel->id());
        $card->set('card_type', 'character');

        return $cards->getConnection()->transactional(function () use (
            $cards,
            $characters,
            $card,
            $characterData,
            $currentNovel,
        ): Character {
            $savedCard = $cards->save($card);
            if ($savedCard === false) {
                throw new PersistenceFailedException($card, 'Could not save card');
            }

            unset($characterData['id'], $characterData['card_id']);
            /** @var \App\Model\Entity\Character $character */
            $character = $characters->newEntity($characterData);
            $character->set('card_id', (int)$card->get('id'));

            $savedCharacter = $characters->save($character);
            if ($savedCharacter === false) {
                throw new PersistenceFailedException($character, 'Could not save character');
            }

            return $this->getForNovel($currentNovel, (int)$character->id);
        });
    }

    /**
     * @param array<string, mixed> $characterData
     */
    public function initializeForCard(CurrentNovel $currentNovel, int $cardId, array $characterData): Character
    {
        $this->getCharacterCardForNovel($currentNovel, $cardId);

        $characters = TableRegistry::getTableLocator()->get('Characters');
        if ($characters->exists(['card_id' => $cardId])) {
            throw new NotFoundException();
        }

        unset($characterData['id'], $characterData['card_id']);
        /** @var \App\Model\Entity\Character $character */
        $character = $characters->newEntity($characterData);
        $character->set('card_id', $cardId);

        $savedCharacter = $characters->save($character);
        if ($savedCharacter === false) {
            throw new PersistenceFailedException($character, 'Could not save character');
        }

        return $this->getForNovel($currentNovel, (int)$character->id);
    }

    /**
     * @param array<string, mixed> $cardData
     * @param array<string, mixed> $characterData
     * @param array<string, mixed> $appearanceData
     * @param array<string, mixed> $personalityData
     * @param array<string, mixed> $voiceData
     * @param array<int, array<string, mixed>> $traitsData
     * @param array<int, array<string, mixed>> $skillsData
     * @param array<int, array<string, mixed>> $goalsData
     */
    public function update(
        CurrentNovel $currentNovel,
        int $characterId,
        array $cardData,
        array $characterData,
        array $appearanceData = [],
        array $personalityData = [],
        array $voiceData = [],
        array $traitsData = [],
        array $skillsData = [],
        array $goalsData = [],
    ): Character {
        $characters = TableRegistry::getTableLocator()->get('Characters');
        $cards = TableRegistry::getTableLocator()->get('Cards');
        $characterAppearances = TableRegistry::getTableLocator()->get('CharacterAppearances');
        $characterPersonalities = TableRegistry::getTableLocator()->get('CharacterPersonalities');
        $characterVoices = TableRegistry::getTableLocator()->get('CharacterVoices');
        $characterTraits = TableRegistry::getTableLocator()->get('CharacterTraits');
        $characterSkills = TableRegistry::getTableLocator()->get('CharacterSkills');
        $characterGoals = TableRegistry::getTableLocator()->get('CharacterGoals');

        $character = $this->getForNovel($currentNovel, $characterId);
        if (!$character->card instanceof Card) {
            throw new NotFoundException();
        }

        unset($cardData['id'], $cardData['novel_id'], $cardData['card_type']);
        unset($characterData['id'], $characterData['card_id']);

        $cards->patchEntity($character->card, $cardData);
        $characters->patchEntity($character, $characterData);

        return $cards->getConnection()->transactional(function () use (
            $cards,
            $characters,
            $characterAppearances,
            $characterPersonalities,
            $characterVoices,
            $characterTraits,
            $characterSkills,
            $characterGoals,
            $character,
            $currentNovel,
            $appearanceData,
            $personalityData,
            $voiceData,
            $traitsData,
            $skillsData,
            $goalsData,
        ): Character {
            if ($cards->save($character->card) === false) {
                throw new PersistenceFailedException($character->card, 'Could not save card');
            }
            if ($characters->save($character) === false) {
                throw new PersistenceFailedException($character, 'Could not save character');
            }

            $this->saveOptionalSection(
                $characterAppearances,
                $character,
                'character_appearance',
                $appearanceData,
                'Could not save character appearance',
            );
            $this->saveOptionalSection(
                $characterPersonalities,
                $character,
                'character_personality',
                $personalityData,
                'Could not save character personality',
            );
            $this->saveOptionalSection(
                $characterVoices,
                $character,
                'character_voice',
                $voiceData,
                'Could not save character voice',
            );
            $this->syncTraitRows($characterTraits, $character, $traitsData);
            $this->syncSkillRows($characterSkills, $character, $skillsData);
            $this->syncGoalRows($characterGoals, $character, $goalsData);

            return $this->getForNovel($currentNovel, (int)$character->id);
        });
    }

    public function getForNovel(CurrentNovel $currentNovel, int $characterId): Character
    {
        $characters = TableRegistry::getTableLocator()->get('Characters');

        try {
            /** @var \App\Model\Entity\Character $character */
            $character = $characters->find()
                ->innerJoinWith('Cards', function ($query) use ($currentNovel) {
                    return $query->where([
                        'Cards.novel_id' => $currentNovel->id(),
                        'Cards.card_type' => 'character',
                    ]);
                })
                ->contain([
                    'Cards',
                    'CharacterAppearances',
                    'CharacterPersonalities',
                    'CharacterVoices',
                    'CharacterTraits' => function ($query) {
                        return $query->orderBy([
                            'CharacterTraits.sort_order' => 'ASC',
                            'CharacterTraits.id' => 'ASC',
                        ]);
                    },
                    'CharacterSkills' => function ($query) {
                        return $query->orderBy([
                            'CharacterSkills.sort_order' => 'ASC',
                            'CharacterSkills.id' => 'ASC',
                        ]);
                    },
                    'CharacterGoals' => function ($query) {
                        return $query->orderBy([
                            'CharacterGoals.priority' => 'ASC',
                            'CharacterGoals.id' => 'ASC',
                        ]);
                    },
                ])
                ->where([
                    'Characters.id' => $characterId,
                ])
                ->firstOrFail();
        } catch (RecordNotFoundException) {
            throw new NotFoundException();
        }

        return $character;
    }

    public function getCharacterCardForNovel(CurrentNovel $currentNovel, int $cardId): Card
    {
        $cards = TableRegistry::getTableLocator()->get('Cards');

        try {
            /** @var \App\Model\Entity\Card $card */
            $card = $cards->find()
                ->where([
                    'Cards.id' => $cardId,
                    'Cards.novel_id' => $currentNovel->id(),
                    'Cards.card_type' => 'character',
                ])
                ->firstOrFail();
        } catch (RecordNotFoundException) {
            throw new NotFoundException();
        }

        return $card;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function saveOptionalSection(
        Table $table,
        Character $character,
        string $property,
        array $data,
        string $errorMessage,
    ): void {
        /** @var \Cake\Datasource\EntityInterface|null $existing */
        $existing = $character->get($property);

        if ($existing === null) {
            if (!$this->hasSubmittedValues($data)) {
                return;
            }
            $entity = $table->newEntity($data);
            $entity->set('character_id', (int)$character->id);
        } else {
            $entity = $this->assignSectionValues($existing, $data);
        }

        if ($table->save($entity) === false) {
            throw new PersistenceFailedException($entity, $errorMessage);
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    private function hasSubmittedValues(array $data): bool
    {
        foreach ($data as $value) {
            if (is_string($value) && trim($value) !== '') {
                return true;
            }
            if ($value !== null && !is_string($value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function assignSectionValues(EntityInterface $entity, array $data): EntityInterface
    {
        foreach ($data as $field => $value) {
            if (!is_string($field)) {
                continue;
            }
            $entity->set($field, $value);
        }

        return $entity;
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     */
    private function syncTraitRows(Table $table, Character $character, array $rows): void
    {
        $this->syncRepeatableRows(
            $table,
            $character,
            $rows,
            'character_traits',
            'sort_order',
            'Could not save character trait',
        );
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     */
    private function syncSkillRows(Table $table, Character $character, array $rows): void
    {
        $this->syncRepeatableRows(
            $table,
            $character,
            $rows,
            'character_skills',
            'sort_order',
            'Could not save character skill',
        );
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     */
    private function syncGoalRows(Table $table, Character $character, array $rows): void
    {
        $this->syncRepeatableRows(
            $table,
            $character,
            $rows,
            'character_goals',
            'priority',
            'Could not save character goal',
        );
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     */
    private function syncRepeatableRows(
        Table $table,
        Character $character,
        array $rows,
        string $property,
        string $orderField,
        string $errorMessage,
    ): void {
        $existingById = [];
        $existingRows = $character->get($property);
        if (is_iterable($existingRows)) {
            foreach ($existingRows as $existing) {
                if (!$existing instanceof EntityInterface) {
                    continue;
                }
                $existingId = $existing->get('id');
                if ($existingId !== null) {
                    $existingById[(int)$existingId] = $existing;
                }
            }
        }

        foreach ($rows as $index => $rowData) {
            if (!is_array($rowData)) {
                continue;
            }

            $deleteRequested = $this->isDeleteRequested($rowData);
            $rowId = $this->extractOptionalId($rowData['id'] ?? null);

            if ($rowId !== null) {
                if (!isset($existingById[$rowId])) {
                    throw new NotFoundException();
                }
                $entity = $existingById[$rowId];
            } else {
                $submittedValues = $rowData;
                unset(
                    $submittedValues['id'],
                    $submittedValues['character_id'],
                    $submittedValues['delete'],
                    $submittedValues[$orderField],
                );
                if ($deleteRequested || !$this->hasSubmittedValues($submittedValues)) {
                    continue;
                }
                $entity = $table->newEmptyEntity();
                $entity->set('character_id', (int)$character->id);
            }

            if ($deleteRequested) {
                if ($table->delete($entity) === false) {
                    throw new PersistenceFailedException($entity, $errorMessage);
                }

                continue;
            }

            $rowData = $this->normalizeOrderField($rowData, $orderField, (int)$index);
            unset($rowData['id'], $rowData['delete']);
            $rowData['character_id'] = (int)$character->id;

            $entity = $table->patchEntity($entity, $rowData);
            $entity->set('character_id', (int)$character->id);

            if ($table->save($entity) === false) {
                throw new PersistenceFailedException($entity, $errorMessage);
            }
        }
    }

    /**
     * @param array<string, mixed> $rowData
     * @return array<string, mixed>
     */
    private function normalizeOrderField(array $rowData, string $orderField, int $fallbackOrder): array
    {
        if (
            !array_key_exists($orderField, $rowData)
            || $rowData[$orderField] === ''
            || $rowData[$orderField] === null
        ) {
            $rowData[$orderField] = $fallbackOrder;
        }

        return $rowData;
    }

    /**
     * @param mixed $value
     */
    private function extractOptionalId(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int)$value;
    }

    /**
     * @param array<string, mixed> $rowData
     */
    private function isDeleteRequested(array $rowData): bool
    {
        if (!array_key_exists('delete', $rowData)) {
            return false;
        }

        $value = $rowData['delete'];
        if (is_bool($value)) {
            return $value;
        }
        if (is_int($value)) {
            return $value === 1;
        }
        if (is_string($value)) {
            $normalized = strtolower(trim($value));

            return in_array($normalized, ['1', 'true', 'on', 'yes'], true);
        }

        return false;
    }
}
