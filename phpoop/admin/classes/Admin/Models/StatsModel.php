<?php
declare(strict_types=1);

namespace Admin\Models;

class StatsModel
{
    /**
     * getStats()
     *
     * Doel:
     * Levert statistieken voor het dashboard.
     *
     * Werking:
     * - Geeft voorlopig dummy data terug.
     * - Later vervangen we dit door echte database queries.
     */
    public function getStats(): array
    {
        return [
            'posts' => 2,
            'users' => 5,
            'views' => 1234,
        ];
    }
}
