<?php
declare(strict_types=1);

namespace Admin\Controllers;

use Admin\Core\View;
use Admin\Models\StatsModel;

class DashboardController
{
    private StatsModel $statsModel;
    private string $title = 'Dashboard';

    /**
     * __construct()
     *
     * Doel:
     * Bewaart StatsModel zodat index() stats kan ophalen.
     */
    public function __construct(StatsModel $statsModel)
    {
        $this->statsModel = $statsModel;
    }

    /**
     * index()
     *
     * Doel:
     * Toont het dashboard.
     *
     * Werking:
     * 1) Vraagt stats op via het model.
     * 2) Rendert dashboard.php via View::render().
     * 3) Geeft $title en $stats door aan de view.
     */
    public function index(): void
    {
        $stats = $this->statsModel->getStats();

        View::render('dashboard.php', [
            'title' => $this->title,
            'stats' => $stats,
        ]);
    }
}
