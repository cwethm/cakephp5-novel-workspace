<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

class CharactersControllerTest extends TestCase
{
    use IntegrationTestTrait;

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

    private function loginAsUserA(): void
    {
        $user = TableRegistry::getTableLocator()->get('Users')->get(1);
        $this->session(['Auth' => $user]);
    }

    public function testAddCreatesCardAndCharacterAtomically(): void
    {
        $this->loginAsUserA();

        $cards = TableRegistry::getTableLocator()->get('Cards');
        $characters = TableRegistry::getTableLocator()->get('Characters');

        $beforeCards = $cards->find()->count();
        $beforeCharacters = $characters->find()->count();

        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post('/novels/1/characters/add', [
            'card' => [
                'name' => 'Mara Quill',
                'short_summary' => 'Detective',
                'description' => 'Lead investigator',
                'importance' => 'normal',
                'status' => 'active',
                'novel_id' => 2,
                'card_type' => 'location',
            ],
            'character' => [
                'role' => 'protagonist',
                'occupation' => 'Detective',
            ],
        ]);

        $this->assertResponseSuccess();
        $this->assertRedirectContains('/novels/1/characters/');

        $this->assertSame($beforeCards + 1, $cards->find()->count());
        $this->assertSame($beforeCharacters + 1, $characters->find()->count());

        $card = $cards->find()->where(['Cards.name' => 'Mara Quill'])->firstOrFail();
        $this->assertSame(1, (int)$card->novel_id);
        $this->assertSame('character', $card->card_type);

        $character = $characters->find()->where(['Characters.card_id' => $card->id])->firstOrFail();
        $this->assertSame('protagonist', (string)$character->role);
    }

    public function testViewForeignNovelCharacterReturns404(): void
    {
        $this->loginAsUserA();

        $this->get('/novels/1/characters/2');

        $this->assertResponseCode(404);
    }

    public function testInitializeCreatesSubtypeForExistingCharacterCard(): void
    {
        $this->loginAsUserA();

        $cards = TableRegistry::getTableLocator()->get('Cards');
        $characters = TableRegistry::getTableLocator()->get('Characters');

        $card = $cards->newEntity([
            'novel_id' => 1,
            'card_type' => 'character',
            'name' => 'Init Only Card',
            'status' => 'active',
        ]);
        $cards->saveOrFail($card);

        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post('/novels/1/characters/initialize/' . $card->id, [
            'character' => [
                'role' => 'supporting',
            ],
        ]);

        $this->assertResponseSuccess();
        $this->assertRedirectContains('/novels/1/characters/');
        $this->assertTrue($characters->exists(['card_id' => (int)$card->id]));
    }

    public function testInitializeRejectsNonCharacterCard(): void
    {
        $this->loginAsUserA();

        $cards = TableRegistry::getTableLocator()->get('Cards');

        $card = $cards->newEntity([
            'novel_id' => 1,
            'card_type' => 'location',
            'name' => 'North Dock',
            'status' => 'active',
        ]);
        $cards->saveOrFail($card);

        $this->get('/novels/1/characters/initialize/' . $card->id);

        $this->assertResponseCode(404);
    }

    public function testInitializeRejectsForeignNovelCard(): void
    {
        $this->loginAsUserA();

        $this->get('/novels/1/characters/initialize/2');

        $this->assertResponseCode(404);
    }

    public function testInitializeRejectsCardThatAlreadyHasCharacter(): void
    {
        $this->loginAsUserA();

        $this->get('/novels/1/characters/initialize/1');

        $this->assertResponseCode(404);
    }

    public function testEditUpdatesCardAndCharacter(): void
    {
        $this->loginAsUserA();

        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->put('/novels/1/characters/1/edit', [
            'card' => [
                'name' => 'Whitehope Updated',
                'status' => 'archived',
            ],
            'character' => [
                'role' => 'mentor',
            ],
        ]);

        $this->assertResponseSuccess();
        $this->assertRedirectContains('/novels/1/characters/1');

        $cards = TableRegistry::getTableLocator()->get('Cards');
        $characters = TableRegistry::getTableLocator()->get('Characters');

        $card = $cards->get(1);
        $this->assertSame('Whitehope Updated', (string)$card->name);
        $this->assertSame('archived', (string)$card->status);

        $character = $characters->get(1);
        $this->assertSame('mentor', (string)$character->role);
    }

    public function testEditCreatesMissingSectionsWhenSubmitted(): void
    {
        $this->loginAsUserA();

        $cards = TableRegistry::getTableLocator()->get('Cards');
        $characters = TableRegistry::getTableLocator()->get('Characters');
        $appearances = TableRegistry::getTableLocator()->get('CharacterAppearances');
        $personalities = TableRegistry::getTableLocator()->get('CharacterPersonalities');
        $voices = TableRegistry::getTableLocator()->get('CharacterVoices');

        $card = $cards->newEntity([
            'novel_id' => 1,
            'card_type' => 'character',
            'name' => 'Section Subject',
            'status' => 'active',
        ]);
        $cards->saveOrFail($card);

        $character = $characters->newEntity([
            'card_id' => (int)$card->id,
            'role' => 'supporting',
        ]);
        $characters->saveOrFail($character);

        $this->assertFalse($personalities->exists(['character_id' => (int)$character->id]));
        $this->assertFalse($voices->exists(['character_id' => (int)$character->id]));

        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->put('/novels/1/characters/' . $character->id . '/edit', [
            'card' => [
                'name' => 'Section Subject Updated',
                'status' => 'active',
            ],
            'character' => [
                'role' => 'lead',
            ],
            'appearance' => [
                'height' => '180 cm',
            ],
            'personality' => [
                'public_self' => 'Confident',
            ],
            'voice' => [
                'accent' => 'Coastal',
            ],
        ]);

        $this->assertResponseSuccess();
        $this->assertRedirectContains('/novels/1/characters/' . $character->id);
        $this->assertTrue($appearances->exists(['character_id' => (int)$character->id]));
        $this->assertTrue($personalities->exists(['character_id' => (int)$character->id]));
        $this->assertTrue($voices->exists(['character_id' => (int)$character->id]));
    }

    public function testEditForeignNovelCharacterReturns404(): void
    {
        $this->loginAsUserA();

        $this->get('/novels/1/characters/2/edit');

        $this->assertResponseCode(404);
    }

    public function testEditRepeatablesCrudAndReorder(): void
    {
        $this->loginAsUserA();

        $traits = TableRegistry::getTableLocator()->get('CharacterTraits');
        $skills = TableRegistry::getTableLocator()->get('CharacterSkills');
        $goals = TableRegistry::getTableLocator()->get('CharacterGoals');

        $traitToDelete = $traits->newEntity([
            'character_id' => 1,
            'trait_type' => 'habit',
            'name' => 'Old Habit',
            'description' => 'To be deleted',
            'sort_order' => 9,
        ]);
        $traits->saveOrFail($traitToDelete);

        $skillToDelete = $skills->newEntity([
            'character_id' => 1,
            'name' => 'Old Skill',
            'description' => 'To be deleted',
            'proficiency' => 'basic',
            'sort_order' => 9,
        ]);
        $skills->saveOrFail($skillToDelete);

        $goalToDelete = $goals->newEntity([
            'character_id' => 1,
            'goal_type' => 'external',
            'description' => 'To be deleted',
            'priority' => 9,
            'status' => 'active',
        ]);
        $goals->saveOrFail($goalToDelete);

        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->put('/novels/1/characters/1/edit', [
            'card' => [
                'name' => 'Whitehope Updated',
                'status' => 'active',
            ],
            'character' => [
                'role' => 'protagonist',
            ],
            'traits' => [
                [
                    'id' => 1,
                    'trait_type' => 'strength',
                    'name' => 'Sharper Observer',
                    'description' => 'Improved detail tracking',
                    'sort_order' => 2,
                ],
                [
                    'id' => (int)$traitToDelete->id,
                    'delete' => 1,
                ],
                [
                    'trait_type' => 'fear',
                    'name' => 'Crowds',
                    'description' => 'Avoids dense crowds',
                    'sort_order' => 1,
                ],
            ],
            'skills' => [
                [
                    'id' => 1,
                    'name' => 'Investigation',
                    'description' => 'Refined process',
                    'proficiency' => 'expert',
                    'sort_order' => 2,
                ],
                [
                    'id' => (int)$skillToDelete->id,
                    'delete' => 1,
                ],
                [
                    'name' => 'Disguise',
                    'description' => 'Can blend into markets',
                    'proficiency' => 'intermediate',
                    'sort_order' => 1,
                ],
            ],
            'goals' => [
                [
                    'id' => 1,
                    'goal_type' => 'external',
                    'description' => 'Expose the ring leaders',
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
        ]);

        $this->assertResponseSuccess();
        $this->assertRedirectContains('/novels/1/characters/1');

        $this->assertFalse($traits->exists(['id' => (int)$traitToDelete->id]));
        $this->assertFalse($skills->exists(['id' => (int)$skillToDelete->id]));
        $this->assertFalse($goals->exists(['id' => (int)$goalToDelete->id]));

        $traitDescriptions = $traits->find()
            ->where(['character_id' => 1])
            ->orderBy(['sort_order' => 'ASC', 'id' => 'ASC'])
            ->all()
            ->extract('description')
            ->toList();
        $this->assertSame('Avoids dense crowds', (string)$traitDescriptions[0]);

        $skillNames = $skills->find()
            ->where(['character_id' => 1])
            ->orderBy(['sort_order' => 'ASC', 'id' => 'ASC'])
            ->all()
            ->extract('name')
            ->toList();
        $this->assertSame('Disguise', (string)$skillNames[0]);

        $goalDescriptions = $goals->find()
            ->where(['character_id' => 1])
            ->orderBy(['priority' => 'ASC', 'id' => 'ASC'])
            ->all()
            ->extract('description')
            ->toList();
        $this->assertSame('Protect witnesses', (string)$goalDescriptions[0]);
    }

    public function testEditRejectsForeignRepeatableRowIds(): void
    {
        $this->loginAsUserA();

        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->put('/novels/1/characters/1/edit', [
            'card' => [
                'name' => 'Whitehope Updated',
                'status' => 'active',
            ],
            'character' => [
                'role' => 'protagonist',
            ],
            'traits' => [
                [
                    'id' => 2,
                    'trait_type' => 'strength',
                    'name' => 'Invalid Foreign Row',
                    'description' => 'Should reject',
                    'sort_order' => 0,
                ],
            ],
        ]);

        $this->assertResponseCode(404);
    }
}
