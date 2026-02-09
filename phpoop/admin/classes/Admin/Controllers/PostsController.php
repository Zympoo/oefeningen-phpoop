<?php
declare(strict_types=1);

namespace Admin\Controllers;

use Admin\Core\Auth;
use Admin\Core\Flash;
use Admin\Core\View;
use Admin\Repositories\MediaRepository;
use Admin\Repositories\PostsRepository;
use Admin\Services\SlugService;

final class PostsController
{
    private PostsRepository $posts;

    public function __construct(PostsRepository $posts)
    {
        $this->posts = $posts;
    }

    public function index(): void
    {
        $this->posts->clearLocksForUser($_SESSION['user_id']);

        View::render('posts.php', [
            'title' => 'Posts',
            'posts' => $this->posts->getAll(),
        ]);
    }

    public function show(int $id): void
    {
        $post = $this->posts->find($id);

        if (!$post) {
            Flash::set('error', 'Post niet gevonden.');
            header('Location: ' . ADMIN_BASE_PATH . '/posts');
            exit;
        }

        View::render('post-show.php', [
            'title' => 'Post bekijken',
            'post' => $post,
        ]);
    }

    public function create(): void
    {
        $old = Flash::get('old');
        if (!is_array($old)) {
            $old = [
                'title' => '',
                'content' => '',
                'status' => 'draft',
                'publishDate' => '',
                'featured_media_id' => '',
                'meta_title' => '',
                'meta_description' => '',
            ];
        }

        View::render('post-create.php', [
            'title' => 'Nieuwe post',
            'old' => $old,
            'media' => MediaRepository::make()->getAllImages(),
        ]);
    }

    public function store(): void
    {
        $title           = trim((string)($_POST['title'] ?? ''));
        $content         = trim((string)($_POST['content'] ?? ''));
        $status          = (string)($_POST['status'] ?? 'draft');
        $toPublishAt     = trim((string)($_POST['publishDate'] ?? ''));
        $featuredRaw     = trim((string)($_POST['featured_media_id'] ?? ''));
        $metaTitle = trim((string)($_POST['meta_title'] ?? ''));
        $metaTitle = $metaTitle === '' ? null : $metaTitle;

        $metaDescription = trim((string)($_POST['meta_description'] ?? ''));
        $metaDescription = $metaDescription === '' ? null : $metaDescription;

        $toPublishAt = $toPublishAt === '' ? null : $toPublishAt;
        $featuredId = $this->normalizeFeaturedId($featuredRaw);

        $errors = $this->validate($title, $content, $status, $featuredId, $toPublishAt, $metaTitle, $metaDescription);

        if (!empty($errors)) {
            Flash::set('warning', $errors);
            Flash::set('old', compact('title','content','status','toPublishAt','metaTitle','metaDescription') + ['featured_media_id'=>$featuredRaw]);
            header('Location: ' . ADMIN_BASE_PATH . '/posts/create');
            exit;
        }

        $slug = SlugService::TitleToSlug($title);
        $slug = $this->posts->getUniqueSlug($slug);

        $this->posts->create($title, $content, $status, $slug, $featuredId, $toPublishAt, $metaTitle, $metaDescription);

        Flash::set('success', 'Post succesvol aangemaakt.');
        header('Location: ' . ADMIN_BASE_PATH . '/posts');
        exit;
    }

    public function edit(int $id): void
    {
        $post = $this->posts->find($id);
        if (!$post) {
            Flash::set('error', 'Post niet gevonden.');
            header('Location: ' . ADMIN_BASE_PATH . '/posts');
            exit;
        }

        $userId = $_SESSION['user_id'];

        if ($this->posts->isLocked($id, $userId, 15)) {
            Flash::set(
                'error',
                'Deze post wordt momenteel bewerkt door een andere admin.'
            );
            header('Location: ' . ADMIN_BASE_PATH . '/posts');
            exit;
        }

        $old = Flash::get('old');
        if (!is_array($old)) {
            $old = [
                'title' => (string)$post['title'],
                'content' => (string)$post['content'],
                'status' => (string)$post['status'],
                'publishDate' => (string)($post['to_publish_at'] ?? ''),
                'featured_media_id' => (string)($post['featured_media_id'] ?? ''),
                'meta_title' => (string)($post['meta_title'] ?? ''),
                'meta_description' => (string)($post['meta_description'] ?? ''),
            ];
        }


        $this->posts->lock($id, $userId);

        View::render('post-edit.php', [
            'title' => 'Post bewerken',
            'postId' => $id,
            'post' => $post,
            'old' => $old,
            'media' => MediaRepository::make()->getAllImages(),
        ]);
    }

    public function update(int $id): void
    {
        $post = $this->posts->find($id);
        if (!$post) {
            Flash::set('error', 'Post niet gevonden.');
            header('Location: ' . ADMIN_BASE_PATH . '/posts');
            exit;
        }

        $title           = trim((string)($_POST['title'] ?? ''));
        $content         = trim((string)($_POST['content'] ?? ''));
        $status          = (string)($_POST['status'] ?? 'draft');
        $toPublishAt     = trim((string)($_POST['publishDate'] ?? ''));
        $featuredRaw     = trim((string)($_POST['featured_media_id'] ?? ''));

        $metaTitle       = trim((string)($_POST['meta_title'] ?? ''));
        $metaTitle       = $metaTitle === '' ? null : $metaTitle;

        $metaDescription = trim((string)($_POST['meta_description'] ?? ''));
        $metaDescription = $metaDescription === '' ? null : $metaDescription;

        $toPublishAt = $toPublishAt === '' ? null : $toPublishAt;
        $featuredId = $this->normalizeFeaturedId($featuredRaw);

        $errors = $this->validate($title, $content, $status, $featuredId, $toPublishAt, $metaTitle, $metaDescription, $post['id']);

        if (!empty($errors)) {
            Flash::set('warning', $errors);
            Flash::set('old', compact('title','content','status','toPublishAt','metaTitle','metaDescription') + ['featured_media_id'=>$featuredRaw]);
            header('Location: ' . ADMIN_BASE_PATH . '/posts/' . $id . '/edit');
            exit;
        }

        $slug = SlugService::TitleToSlug($title);
        $slug = $this->posts->getUniqueSlug($slug, $id);

        $this->posts->update($id, $title, $content, $status, $slug, $featuredId, $toPublishAt, $metaTitle, $metaDescription);

        $this->posts->unlock($id);

        Flash::set('success', 'Post succesvol aangepast.');
        header('Location: ' . ADMIN_BASE_PATH . '/posts');
        exit;
    }

    public function disable(int $id): void
    {
        if (!Auth::isAdmin()) {
            header('Location: /admin');
            exit;
        }

        $this->posts->disable($id);

        Flash::set('Post verwijderd.', 'success');
        header('Location: /admin/posts');
        exit;
    }

    public function enable(int $id): void
    {
        if (!Auth::isAdmin()) {
            header('Location: /admin');
            exit;
        }

        $this->posts->enable($id);

        Flash::set('Post hersteld.', 'success');
        header('Location: /admin/posts');
        exit;
    }



    public function deleteConfirm(int $id): void
    {
        $post = $this->posts->find($id);

        if (!$post) {
            Flash::set('error', 'Post niet gevonden.');
            header('Location: ' . ADMIN_BASE_PATH . '/posts');
            exit;
        }

        View::render('post-delete.php', [
            'title' => 'Post verwijderen',
            'post' => $post,
        ]);
    }

    private function normalizeFeaturedId(string $raw): ?int
    {
        if ($raw === '' || !ctype_digit($raw)) return null;
        $id = (int)$raw;
        return $id > 0 ? $id : null;
    }

    private function validate(
        string $title,
        string $content,
        string $status,
        ?int $featuredId,
        ?string $toPublishAt,
        ?string $metaTitle,
        ?string $metaDescription,
        ?int $postId = null
    ): array {
        $errors = [];

        if ($title === '') $errors[] = 'Titel is verplicht.';
        elseif (mb_strlen($title) < 3) $errors[] = 'Titel moet minstens 3 tekens bevatten.';

        if ($content === '') $errors[] = 'Inhoud is verplicht.';
        elseif (mb_strlen($content) < 10) $errors[] = 'Inhoud moet minstens 10 tekens bevatten.';

        if (!in_array($status, ['draft','published'], true)) $errors[] = 'Status moet draft of published zijn.';

        if ($toPublishAt !== null) {
            try {
                $publishDate = new \DateTimeImmutable($toPublishAt);
                $now = new \DateTimeImmutable('now');
                if ($publishDate < $now) $errors[] = 'Publicatiedatum mag niet in het verleden liggen.';
            } catch (\Exception $e) {
                $errors[] = 'Ongeldige publicatiedatum.';
            }
        }

        if ($featuredId !== null && MediaRepository::make()->findImageById($featuredId) === null) {
            $errors[] = 'Featured image is ongeldig.';
        }

        if ($metaTitle !== null && mb_strlen($metaTitle) > 70) {
            $errors[] = 'Meta title mag maximaal 70 tekens bevatten.';
        } elseif ($metaTitle !== null && $this->posts->metaTitleExists($metaTitle, $postId)) {
            $errors[] = 'Meta title bestaat al, kies een andere.';
        }

        if ($metaDescription !== null && mb_strlen($metaDescription) > 160) {
            $errors[] = 'Meta description mag maximaal 160 tekens bevatten.';
        }

        return $errors;
    }
}