<?php
declare(strict_types=1);

namespace App\Test\TestCase\Service;

use App\Domain\CurrentNovel;
use App\Service\CharacterService;
use Cake\Http\Exception\NotFoundException;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

class CharacterServiceTest extends TestCase
{
    protected array $fixtures = [
        'app.Users',
        'app.Novels',
        'app.Cards',
        'app.Characters',
        'app.CharacterAppearances',
        'app.CharacterPersonalities',
        'app.CharacterVoices',
        'app.CharacterTraits',
        'app.CharacterSkills',
        'app.CharacterGoals',
    ];

    private function currentNovelOne(): CurrentNovel
    {
        $novels = TableRegistry::getTableLocator()->get('Novels');
        $users = TableRegistry::getTableLocator()->get('Users');

        return new CurrentNovel($novels->get(1), $users->get(1));
    }

    public function testCreatePersistsCardAndCharacter(): void
    {
        $service = new CharacterService();

        $created = $service->create(
            $this->currentNovelOne(),
            [
                'name' => 'New Character Card',
                'status' => 'active',
                'novel_id' => 2,
                'card_type' => 'location',
            ],
            [
                'role' => 'protagonist',
            ],
        );

        $cards = TableRegistry::getTableLocator()->get('Cards');
        $characters = TableRegistry::getTableLocator()->get('Characters');

        $card = $cards->get((int)$created->card_id);
        $this->assertSame(1, (int)$card->novel_id);
        $this->assertSame('character', (string)$card->card_type);

        $this->assertTrue($characters->exists(['id' => (int)$created->id]));
    }

    public function testCreateIsAtomicWhenCharacterValidationFails(): void
    {
        $service = new CharacterService();
        $cards = TableRegistry::getTableLocator()->get('Cards');

        $before = $cards->find()->count();

        try {
            $service->create(
                $this->currentNovelOne(),
                [
                    'name' => 'Should Rollback',
                    'status' => 'active',
                ],
                [
                    'age' => 'invalid-age',
                ],
            );
            $this->fail('Expected PersistenceFailedException was not thrown.');
        } catch (PersistenceFailedException) {
            $this->assertSame($before, $cards->find()->count());
        }
    }

    public function testInitializeRejectsForeignNovelCard(): void
    {
        $service = new CharacterService();

        $this->expectException(NotFoundException::class);
        $service->initializeForCard($this->currentNovelOne(), 2, ['role' => 'supporting']);
    }

    public function testInitializeRejectsNonCharacterCard(): void
    {
        $cards = TableRegistry::getTableLocator()->get('Cards');
        $service = new CharacterService();

        $card = $cards->newEntity([
            'novel_id' => 1,
            'card_type' => 'location',
            'name' => 'Harbor Gate',
            'status' => 'active',
        ]);
        $cards->saveOrFail($card);

        $this->expectException(NotFoundException::class);
        $service->initializeForCard($this->currentNovelOne(), (int)$card->id, ['role' => 'supporting']);
    }

    public function testInitializeRejectsDuplicateCharacterPerCard(): void
    {
        $service = new CharacterService();

        $this->expectException(NotFoundException::class);
        $service->initializeForCard($this->currentNovelOne(), 1, ['role' => 'supporting']);
    }

    public function testUpdateCreatesMissingSectionsWhenSubmitted(): void
    {
        $cards = TableRegistry::getTableLocator()->get('Cards');
        $characters = TableRegistry::getTableLocator()->get('Characters');
        $personalities = TableRegistry::getTableLocator()->get('CharacterPersonalities');
        $voices = TableRegistry::getTableLocator()->get('CharacterVoices');

        $card = $cards->newEntity([
            'novel_id' => 1,
            'card_type' => 'character',
            'name' => 'Section Service Subject',
            'status' => 'active',
        ]);
        $cards->saveOrFail($card);

        $character = $characters->newEntity([
            'card_id' => (int)$card->id,
            'role' => 'supporting',
        ]);
        $characters->saveOrFail($character);

        $service = new CharacterService();
        $service->update(
            $this->currentNovelOne(),
            (int)$character->id,
            ['name' => 'Section Service Subject'],
            ['role' => 'lead'],
            ['height' => '181 cm'],
            ['public_self' => 'Steady'],
            ['accent' => 'Northern'],
        );

        $this->assertTrue($personalities->exists(['character_id' => (int)$character->id]));
        $this->assertTrue($voices->exists(['character_id' => (int)$character->id]));
    }

    public function testUpdateDoesNotCreateMissingSectionsWhenBlank(): void
    {
        $cards = TableRegistry::getTableLocator()->get('Cards');
        $characters = TableRegistry::getTableLocator()->get('Characters');
        $personalities = TableRegistry::getTableLocator()->get('CharacterPersonalities');
        $voices = TableRegistry::getTableLocator()->get('CharacterVoices');

        $card = $cards->newEntity([
            'novel_id' => 1,
            'card_type' => 'character',
            'name' => 'No Section Subject',
            'status' => 'active',
        ]);
        $cards->saveOrFail($card);

        $character = $characters->newEntity([
            'card_id' => (int)$card->id,
            'role' => 'supporting',
        ]);
        $characters->saveOrFail($character);

        $service = new CharacterService();
        $service->update(
            $this->currentNovelOne(),
            (int)$character->id,
            ['name' => 'No Section Subject'],
            ['role' => 'supporting'],
            [],
            ['public_self' => ''],
            ['accent' => ''],
        );

        $this->assertFalse($personalities->exists(['character_id' => (int)$character->id]));
        $this->assertFalse($voices->exists(['character_id' => (int)$character->id]));
    }

    public function testUpdateRollsBackWhenSectionValidationFails(): void
    {
        $cards = TableRegistry::getTableLocator()->get('Cards');
        $characters = TableRegistry::getTableLocator()->get('Characters');
        $personalities = TableRegistry::getTableLocator()->get('CharacterPersonalities');

        $card = $cards->newEntity([
            'novel_id' => 1,
            'card_type' => 'character',
            'name' => 'Rollback Subject',
            'status' => 'active',
        ]);
        $cards->saveOrFail($card);

        $character = $characters->newEntity([
            'card_id' => (int)$card->id,
            'role' => 'supporting',
        ]);
        $characters->saveOrFail($character);

        $service = new CharacterService();

        try {
            $service->update(
                $this->currentNovelOne(),
                (int)$character->id,
                ['name' => 'Rollback Changed'],
                ['role' => 'lead'],
                ['height' => str_repeat('x', 65)],
                ['public_self' => 'Would rollback'],
                [],
            );
            $this->fail('Expected PersistenceFailedException was not thrown.');
        } catch (PersistenceFailedException) {
            $this->assertSame('Rollback Subject', (string)$cards->get((int)$card->id)->name);
            $this->assertSame('supporting', (string)$characters->get((int)$character->id)->role);
            $this->assertFalse($personalities->exists(['character_id' => (int)$character->id]));
        }
    }

    public function testUpdateRepeatablesCrudAndReorder(): void
    {
        $traits = TableRegistry::getTableLocator()->get('CharacterTraits');
        $skills = TableRegistry::getTableLocator()->get('CharacterSkills');
        $goals = TableRegistry::getTableLocator()->get('CharacterGoals');

        $traitToDelete = $traits->newEntity([
            'character_id' => 1,
            'trait_type' => 'habit',
            'name' => 'Old Habit',
            'description' => 'to delete',
            'sort_order' => 9,
        ]);
        $traits->saveOrFail($traitToDelete);

        $skillToDelete = $skills->newEntity([
            'character_id' => 1,
            'name' => 'Old Skill',
            'description' => 'to delete',
            'proficiency' => 'basic',
            'sort_order' => 9,
        ]);
        $skills->saveOrFail($skillToDelete);

        $goalToDelete = $goals->newEntity([
            'character_id' => 1,
            'goal_type' => 'external',
            'description' => 'to delete',
            'priority' => 9,
            'status' => 'active',
        ]);
        $goals->saveOrFail($goalToDelete);

        $service = new CharacterService();
        try {
            $service->update(
                $this->currentNovelOne(),
                1,
                ['name' => 'Whitehope'],
                ['role' => 'protagonist'],
                [],
                [],
                [],
                [
                    [
                        'id' => 1,
                        'trait_type' => 'strength',
                        'name' => 'Sharper Observer',
                        'description' => 'improved',
                        'sort_order' => 2,
                    ],
                    [
                        'id' => (int)$traitToDelete->id,
                        'delete' => 1,
                    ],
                    [
                        'trait_type' => 'fear',
                        'name' => 'Crowds',
                        'description' => 'avoids crowds',
                        'sort_order' => 1,
                    ],
                ],
                [
                    [
                        'id' => 1,
                        'name' => 'Investigation',
                        'description' => 'refined',
                        'proficiency' => 'expert',
                        'sort_order' => 2,
                    ],
                    [
                        'id' => (int)$skillToDelete->id,
                        'delete' => 1,
                    ],
                    [
                        'name' => 'Disguise',
                        'description' => 'market blending',
                        'proficiency' => 'intermediate',
                        'sort_order' => 1,
                    ],
                ],
                [
                    [
                        'id' => 1,
                        'goal_type' => 'external',
                        'description' => 'Expose ring leaders',
                        'priority' => 2,
                        'status' => 'active',
                    ],
                    [
                        'id' => (int)$goalToDelete->id,
                        'delete' => 1,
                    ],
                    [
                        'goal_type' => 'external',
                        'description' => 'Protect witnesses',
                        'priority' => 1,
                        'status' => 'active',
                    ],
                ],
            );
        } catch (PersistenceFailedException $exception) {
            $this->fail(json_encode($exception->getEntity()->getErrors(), JSON_THROW_ON_ERROR));
        }

        $this->assertFalse($traits->exists(['id' => (int)$traitToDelete->id]));
        $this->assertFalse($skills->exists(['id' => (int)$skillToDelete->id]));
        $this->assertFalse($goals->exists(['id' => (int)$goalToDelete->id]));

        $traitDescriptions = $traits->find()
            ->where(['character_id' => 1])
            ->orderBy(['sort_order' => 'ASC', 'id' => 'ASC'])
            ->all()
            ->extract('description')
            ->toList();
        $this->assertSame('avoids crowds', (string)$traitDescriptions[0]);

        $goalDescriptions = $goals->find()
            ->where(['character_id' => 1])
            ->orderBy(['priority' => 'ASC', 'id' => 'ASC'])
            ->all()
            ->extract('description')
            ->toList();
        $this->assertSame('Protect witnesses', (string)$goalDescriptions[0]);
    }

    public function testUpdateRejectsForeignRepeatableRowId(): void
    {
        $service = new CharacterService();

        $this->expectException(NotFoundException::class);
        $service->update(
            $this->currentNovelOne(),
            1,
            ['name' => 'Whitehope'],
            ['role' => 'protagonist'],
            [],
            [],
            [],
            [
                [
                    'id' => 2,
                    'trait_type' => 'strength',
                    'name' => 'Invalid Foreign',
                    'description' => 'should reject',
                    'sort_order' => 0,
                ],
            ],
        );
    }

    public function testUpdateRejectsInvalidGoalMachineKey(): void
    {
        $service = new CharacterService();

        $this->expectException(PersistenceFailedException::class);
        $service->update(
            $this->currentNovelOne(),
            1,
            ['name' => 'Whitehope'],
            ['role' => 'protagonist'],
            [],
            [],
            [],
            [],
            [],
            [
                [
                    'goal_type' => 'internal',
                    'description' => 'invalid type',
                    'priority' => 0,
                    'status' => 'active',
                ],
            ],
        );
    }
}
