<?php
declare(strict_types=1);

namespace Admin\Services;
class StatsService
{
    public function getStats(): array
    {
        return [
            'posts' => 12,
            'users' => 3,
            'views' => 1245, 'comments' => 55,
        ];
    }
}
