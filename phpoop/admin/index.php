<?php
declare(strict_types=1);

/**
 * Start de PHP session.
 * Zonder dit werkt $_SESSION niet.
 */
session_start();

require __DIR__ . '/config/app.php';
require __DIR__ . '/autoload.php';

use Admin\Controllers\AuthController;
use Admin\Controllers\DashboardController;
use Admin\Controllers\ErrorController;
use Admin\Controllers\MediaController;
use Admin\Controllers\PostsController;
use Admin\Controllers\UsersController;
use Admin\Core\Auth;
use Admin\Core\Flash;
use Admin\Core\Router;
use Admin\Models\StatsModel;
use Admin\Repositories\MediaRepository;
use Admin\Repositories\PostsRepository;
use Admin\Repositories\RolesRepository;
use Admin\Repositories\UsersRepository;

// Path uit de URL halen (zonder querystring)
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';

// ADMIN_BASE_PATH (/admin) verwijderen uit de URL
if (str_starts_with($uri, ADMIN_BASE_PATH)) {
    $uri = substr($uri, strlen(ADMIN_BASE_PATH));
}

// Trailing slash verwijderen en lege string corrigeren
$uri = rtrim($uri, '/') ?: '/';

/**
 * Beveilig admin-routes:
 * als je niet ingelogd bent, ga naar /login
 */
$publicRoutes = [
    '/login',
    '/login/github',
    '/login/github/callback',
];

if (!Auth::check() && !in_array($uri, $publicRoutes, true)) {
    header('Location: ' . ADMIN_BASE_PATH . '/login');
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

$router = new Router();

/**
 * setNotFoundHandler()
 *
 * Doel:
 * - Zorgt dat elke onbekende URL een nette 404 pagina krijgt via ErrorController.
 */
$errorController = new ErrorController();

$router->setNotFoundHandler(function (string $requestedUri) use ($errorController): void {
    $errorController->notFound($requestedUri);
});

/**
 * Admin-only guard helper
 *
 * Doel:
 * - herbruikbaar in routes
 * - toont 403 pagina via ErrorController
 */
$requireAdmin = function () use ($errorController): void {
    if (!Auth::isAdmin()) {
        $errorController->forbidden('Admin rechten vereist.');
        exit;
    }
};

/**
 * Dashboard
 */
$router->get('/', function (): void {
    (new DashboardController(new StatsModel()))->index();
});

/**
 * Auth
 */
$router->get('/login', function (): void {
    (new AuthController(UsersRepository::make()))->showLogin();
});

$router->post('/login', function (): void {
    (new AuthController(UsersRepository::make()))->login();
});

// GitHub OAuth login
$router->get('/login/github', function (): void {
    (new AuthController(UsersRepository::make()))->redirectToGitHub();
});

$router->get('/login/github/callback', function (): void {
    (new AuthController(UsersRepository::make()))->githubCallback();
});

$router->post('/logout', function (): void {
    (new AuthController(UsersRepository::make()))->logout();
});

/**
 * Users (admin-only)
 */
$router->get('/users', function () use ($requireAdmin): void {
    $requireAdmin();
    (new UsersController(UsersRepository::make(), RolesRepository::make()))->index();
});

$router->get('/users/create', function () use ($requireAdmin): void {
    $requireAdmin();
    (new UsersController(UsersRepository::make(), RolesRepository::make()))->create();
});

$router->post('/users/store', function () use ($requireAdmin): void {
    $requireAdmin();
    (new UsersController(UsersRepository::make(), RolesRepository::make()))->store();
});

$router->get('/users/{id}/edit', function (int $id) use ($requireAdmin): void {
    $requireAdmin();
    (new UsersController(UsersRepository::make(), RolesRepository::make()))->edit($id);
});

$router->post('/users/{id}/update', function (int $id) use ($requireAdmin): void {
    $requireAdmin();
    (new UsersController(UsersRepository::make(), RolesRepository::make()))->update($id);
});

$router->post('/users/{id}/reset-password', function (int $id) use ($requireAdmin): void {
    $requireAdmin();
    (new UsersController(UsersRepository::make(), RolesRepository::make()))->resetPassword($id);
});

$router->post('/users/{id}/disable', function (int $id) use ($requireAdmin): void {
    $requireAdmin();
    (new UsersController(UsersRepository::make(), RolesRepository::make()))->disable($id);
});

$router->post('/users/{id}/enable', function (int $id) use ($requireAdmin): void {
    $requireAdmin();
    (new UsersController(UsersRepository::make(), RolesRepository::make()))->enable($id);
});

/**
 * Posts
 */
$router->get('/posts', function (): void {
    (new PostsController(PostsRepository::make()))->index();
});

$router->get('/posts/create', function (): void {
    (new PostsController(PostsRepository::make()))->create();
});

$router->post('/posts/store', function (): void {
    (new PostsController(PostsRepository::make()))->store();
});

$router->get('/posts/{id}/edit', function (int $id): void {
    (new PostsController(PostsRepository::make()))->edit($id);
});

$router->post('/posts/{id}/update', function (int $id): void {
    (new PostsController(PostsRepository::make()))->update($id);
});

$router->post('/posts/{id}/disable', function (int $id) use ($requireAdmin): void {
    $requireAdmin();
    (new PostsController(PostsRepository::make()))->disable($id);
});

$router->post('/posts/{id}/enable', function (int $id) use ($requireAdmin): void {
    $requireAdmin();
    (new PostsController(PostsRepository::make()))->enable($id);
});

/**
 * Delete routes (admin-only)
 * Let op: jij had hier al Auth::isAdmin checks.
 * We vervangen dat door $requireAdmin() voor consistent gedrag (403 pagina).
 */
$router->get('/posts/{id}/delete', function (int $id) use ($requireAdmin): void {
    $requireAdmin();
    (new PostsController(PostsRepository::make()))->deleteConfirm($id);
});

$router->post('/posts/{id}/delete', function (int $id) use ($requireAdmin): void {
    $requireAdmin();

    PostsRepository::make()->delete($id);

    Flash::set('success', 'Post verwijderd.');
    header('Location: ' . ADMIN_BASE_PATH . '/posts');
    exit;
});

/**
 * Show
 * Deze moet onder edit/update staan.
 */
$router->get('/posts/{id}', function (int $id): void {
    (new PostsController(PostsRepository::make()))->show($id);
});
/**
 * Media (admin-only)
 */
$router->get('/media', function () use ($requireAdmin): void {
    $requireAdmin();
    (new MediaController(MediaRepository::make()))->index();
});

$router->get('/media/upload', function () use ($requireAdmin): void {
    $requireAdmin();
    (new MediaController(MediaRepository::make()))->uploadForm();
});

$router->post('/media/store', function () use ($requireAdmin): void {
    $requireAdmin();
    (new MediaController(MediaRepository::make()))->store();
});

$router->post('/media/{id}/delete', function (int $id) use ($requireAdmin): void {
    $requireAdmin();
    (new MediaController(MediaRepository::make()))->delete($id);
});

// Revisions overzicht
$router->get('/posts/{id}/revisions', function (int $id) use ($requireAdmin): void {
    $requireAdmin();
    (new PostsController(PostsRepository::make()))->getRevisions($id);
});

$router->get('/posts/{postId}/revisions/{revisionId}', function (int $id, int $revisionId): void {
    (new PostsController(PostsRepository::make()))->showRevision($id, $revisionId);
});

// Revisions herstellen
$router->post('/posts/{postId}/revisions/{revisionId}/restore', function (int $postId, int $revisionId) use ($requireAdmin): void {
    $requireAdmin();

    PostsRepository::make()->restoreRevision($revisionId);

    Flash::set('success', 'Revision hersteld.');
    header('Location: ' . ADMIN_BASE_PATH . "/posts");
    exit;
});

$router->dispatch($uri, $method);