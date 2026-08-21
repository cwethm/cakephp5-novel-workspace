<?php
declare(strict_types=1);

namespace App\Test\TestCase\Service;

use App\Service\SlugService;
use Cake\TestSuite\TestCase;

class SlugServiceTest extends TestCase
{
    public function testSlugCollisionSuffixingWorks(): void
    {
        $service = new SlugService();
        $existing = ['whitehope', 'whitehope-2'];

        $slug = $service->uniqueWithinNovel('Whitehope', 1, function (string $candidate) use ($existing): bool {
            return in_array($candidate, $existing, true);
        });

        $this->assertSame('whitehope-3', $slug);
    }
}
