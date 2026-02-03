<?php
declare(strict_types=1);

namespace Admin\Services;
class SlugService
{
    public static function TitleToSlug(string $title): string
    {
        $slug = strtolower(trim($title));
        $slug = trim(preg_replace('/[^a-z0-9]+/', '-', $slug), '-');

        if($slug == ''){
            throw new \InvalidArgumentException('Slug kon niet worden gegenereerd.');
        }

        return $slug;
    }
}
