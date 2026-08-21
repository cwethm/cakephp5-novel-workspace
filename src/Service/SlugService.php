<?php
declare(strict_types=1);

namespace App\Service;

use Cake\Utility\Text;

class SlugService
{
    public function slugify(string $value): string
    {
        $slug = strtolower(Text::slug($value, '-'));
        $slug = trim($slug, '-');

        if ($slug === '') {
            return 'untitled';
        }

        return $slug;
    }

    public function uniqueWithinNovel(string $value, int $novelId, callable $exists): string
    {
        $base = $this->slugify($value);
        $slug = $base;
        $suffix = 2;

        while ($exists($slug, $novelId)) {
            $slug = sprintf('%s-%d', $base, $suffix);
            $suffix++;
        }

        return $slug;
    }
}
