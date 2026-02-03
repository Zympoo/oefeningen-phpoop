<?php
declare(strict_types=1);

namespace Admin\Controllers;

use Admin\Core\View;

class ErrorController
{
    /**
     * notFound()
     *
     * Doel:
     * Toont een nette 404 pagina binnen de vaste layout.
     */
    public function notFound(string $requestedUri = ''): void
    {
        http_response_code(404);

        View::render('errors/404.php', [
            'title' => '404 - Pagina niet gevonden',
            'requestedUri' => $requestedUri,
        ]);
    }

    /**
     * forbidden()
     *
     * Doel:
     * Toont een nette 403 pagina binnen de vaste layout.
     *
     * Gebruik:
     * - voor admin-only routes (bv. Users beheer)
     */
    public function forbidden(string $message = 'Je hebt geen rechten om deze pagina te bekijken.'): void
    {
        http_response_code(403);

        View::render('errors/403.php', [
            'title' => '403 - Geen toegang',
            'message' => $message,
        ]);
    }
}
