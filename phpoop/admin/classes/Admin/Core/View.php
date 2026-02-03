<?php
declare(strict_types=1);

namespace Admin\Core;

class View
{
    /**
     * render()
     *
     * Doel:
     * Rendert een view binnen de vaste admin-layout.
     *
     * Werking:
     * 1) Zet $data om naar variabelen voor in de view (bv. $title, $posts, $stats).
     * 2) Bepaalt het pad naar de view-file.
     * 3) Controleert of die view-file bestaat.
     * 4) Laadt de vaste layout-onderdelen:
     *    - header.php
     *    - sidebar.php
     *    - topbar.php
     *    - footer.php
     * 5) Laadt de view content tussen topbar en footer.
     */
    public static function render(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);

        if (!isset($title) || $title === '') {
            $title = 'MiniCMS';
        }

        $viewPath = __DIR__ . '/../../../views/' . ltrim($view, '/');

        if (!file_exists($viewPath)) {
            http_response_code(500);
            echo '<h1>500 - View bestaat niet</h1>';
            return;
        }

        require __DIR__ . '/../../../includes/header.php';
        require __DIR__ . '/../../../includes/sidebar.php';

        echo '<main class="flex-1">';
        require __DIR__ . '/../../../includes/topbar.php';
        require __DIR__ . '/../../../views/partials/flash.php';


        require $viewPath;

        echo '</main>';

        require __DIR__ . '/../../../includes/footer.php';
    }
}
