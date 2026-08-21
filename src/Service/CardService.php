<?php
declare(strict_types=1);

namespace App\Service;

use App\Domain\CurrentNovel;
use App\Model\Entity\Card;
use Cake\Datasource\FactoryLocator;
use Cake\Http\Exception\NotFoundException;

class CardService
{
    public function __construct(
        private SlugService $slugService,
    ) {
    }

    public function rename(CurrentNovel $currentNovel, Card $card, string $name): Card
    {
        $currentNovel->assertContains($card);

        $cards = FactoryLocator::get('Table')->get('Cards');
        $card->name = $name;
        $card->slug = $this->slugService->uniqueWithinNovel($name, $currentNovel->id(), function (string $slug, int $novelId) use ($cards, $card): bool {
            $query = $cards->find()->where([
                'Cards.novel_id' => $novelId,
                'Cards.slug' => $slug,
                'Cards.id !=' => (int)$card->id,
            ]);

            return $query->count() > 0;
        });

        return $cards->saveOrFail($card);
    }

    public function archive(CurrentNovel $currentNovel, Card $card): Card
    {
        $currentNovel->assertContains($card);

        $cards = FactoryLocator::get('Table')->get('Cards');
        $card->status = 'archived';

        return $cards->saveOrFail($card);
    }

    /**
     * @param list<int|string> $tagIds
     */
    public function syncTags(CurrentNovel $currentNovel, Card $card, array $tagIds): Card
    {
        $currentNovel->assertContains($card);

        $tags = FactoryLocator::get('Table')->get('Tags');
        $cards = FactoryLocator::get('Table')->get('Cards');

        $allowedTagIds = $tags->find()
            ->select(['id'])
            ->where([
                'Tags.novel_id' => $currentNovel->id(),
                'Tags.id IN' => $tagIds,
            ])
            ->all()
            ->extract('id')
            ->toList();

        if (count($allowedTagIds) !== count($tagIds)) {
            throw new NotFoundException();
        }

        /** @var \App\Model\Entity\Card $result */
        $result = $cards->patchEntity($card, [
            'tags' => array_map(fn($id) => ['id' => (int)$id], $allowedTagIds),
        ], [
            'associated' => ['Tags'],
        ]);

        return $cards->saveOrFail($result);
    }
}
