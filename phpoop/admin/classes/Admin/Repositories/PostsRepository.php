<?php
declare(strict_types=1);
namespace Admin\Repositories;
use Admin\Core\Database;
use PDO;
final class PostsRepository
{
    public function __construct(private PDO $pdo)
    {
    }
    public static function make(): self
    {
        return new self(Database::getConnection());
    }
// -------------------------
// ADMIN
// -------------------------
    public function getAll(): array
    {
        $this->publishPosts();

        $sql = "SELECT id, title, content, status, featured_media_id, to_publish_at,
                created_at, slug, is_active, meta_title, meta_description
                FROM posts
                ORDER BY id DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function find(int $id): ?array
    {
        $this->publishPosts();

        $sql = "SELECT id, title, content, status, featured_media_id, to_publish_at,
                created_at, slug, is_active, meta_title, meta_description
                FROM posts
                WHERE id = :id
                LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row !== false ? $row : null;
    }

    /**
     * disable()
     * Doel: user blokkeren.
     */
    public function disable(int $id): void
    {
        $sql = "UPDATE posts
                SET is_active = 0
                WHERE id = :id
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
    }

    /**
     * enable()
     * Doel: user deblokkeren.
     */
    public function enable(int $id): void
    {
        $sql = "UPDATE posts
                SET is_active = 1
                WHERE id = :id
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
    }

    public function isLocked(int $postId, int $userId, int $timeoutMinutes): bool
    {
        $sql = "
        SELECT 1
        FROM posts
        WHERE id = :id
          AND locked_by IS NOT NULL
          AND locked_by != :user_id
          AND locked_at > (NOW() - INTERVAL :minutes MINUTE)
        LIMIT 1
    ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id' => $postId,
            'user_id' => $userId,
            'minutes' => $timeoutMinutes,
        ]);

        return (bool)$stmt->fetchColumn();
    }

    public function lock(int $postId, int $userId): void
    {
        $sql = "
        UPDATE posts
        SET locked_by = :user_id,
            locked_at = NOW()
        WHERE id = :id
    ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id' => $postId,
            'user_id' => $userId,
        ]);
    }

    public function unlock(int $postId): void
    {
        $sql = "
        UPDATE posts
        SET locked_by = NULL,
            locked_at = NULL
        WHERE id = :id
    ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $postId]);
    }

    public function create(
        string $title, string $content, string $status, string $slug,
        ?int $featuredMediaId = null, ?string $toPublishAt = null,
        ?string $metaTitle = null, ?string $metaDescription = null
    ): int {
        $sql = "INSERT INTO posts (title, content, status, featured_media_id, to_publish_at,
                created_at, slug, is_active, meta_title, meta_description)
                VALUES (:title, :content, :status, :featured_media_id, :to_publish_at,
                NOW(), :slug, 1, :meta_title, :meta_description)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'title' => $title,
            'content' => $content,
            'status' => $status,
            'featured_media_id' => $featuredMediaId,
            'to_publish_at' => $toPublishAt,
            'slug' => $slug,
            'meta_title' => $metaTitle,
            'meta_description' => $metaDescription,
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function update(
        int $id, string $title, string $content, string $status, string $slug,
        ?int $featuredMediaId = null, ?string $toPublishAt = null,
        ?string $metaTitle = null, ?string $metaDescription = null
    ): void {
        $sql = "UPDATE posts
                SET title = :title,
                    content = :content,
                    status = :status,
                    featured_media_id = :featured_media_id,
                    to_publish_at = :to_publish_at,
                    slug = :slug,
                    meta_title = :meta_title,
                    meta_description = :meta_description
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'title' => $title,
            'content' => $content,
            'status' => $status,
            'featured_media_id' => $featuredMediaId,
            'to_publish_at' => $toPublishAt,
            'slug' => $slug,
            'meta_title' => $metaTitle,
            'meta_description' => $metaDescription,
        ]);
    }

    public function delete(int $id): void
    {
        $sql = "DELETE FROM posts WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
    }
// -------------------------
// FRONTEND (exacte methodnames uit jouw public/index.php)
// -------------------------
    /**
     * Laatste gepubliceerde posts voor homepage
     * Returnt ook:
     * - featured_url (of NULL)
     * - featured_alt (string)
     */
    public function getPublishedLatest(int $limit = 6): array
    {
        $this->publishPosts();

        $limit = max(1, min(50, $limit));
        $sql = "SELECT
                p.id,
                p.title,
                p.content,
                p.to_publish_at,
                p.created_at,
                p.featured_media_id,
                p.slug,
                p.is_active,
                p.meta_title,
                p.meta_description,
                CASE
                    WHEN m.id IS NULL THEN NULL
                    ELSE CONCAT('/', m.path, '/', m.filename)
                END AS featured_url,
                COALESCE(m.alt_text, m.original_name, p.title) AS featured_alt
                FROM posts p
                LEFT JOIN media m ON m.id = p.featured_media_id
                WHERE p.status = 'published' AND p.is_active = 1 
                ORDER BY p.created_at DESC
                LIMIT " . (int)$limit;
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function getPublishedAll(): array
    {
        $this->publishPosts();

        $sql = "SELECT
                p.id,
                p.title,
                p.content,
                p.to_publish_at,
                p.created_at,
                p.featured_media_id,
                p.slug,
                p.is_active,
                p.meta_title,
                p.meta_description,
                CASE
                    WHEN m.id IS NULL THEN NULL
                    ELSE CONCAT('/', m.path, '/', m.filename)
                END AS featured_url,
                COALESCE(m.alt_text, m.original_name, p.title) AS featured_alt
                FROM posts p
                LEFT JOIN media m ON m.id = p.featured_media_id
                WHERE p.status = 'published' AND p.is_active = 1
                ORDER BY p.created_at DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function findPublishedBySlug(string $slug): ?array
    {
        $this->publishPosts();

        $sql = "SELECT
                p.id,
                p.title,
                p.content,
                p.to_publish_at,
                p.created_at,
                p.featured_media_id,
                p.slug,
                p.is_active,
                p.meta_title,
                p.meta_description,
                CASE
                    WHEN m.id IS NULL THEN NULL
                    ELSE CONCAT('/', m.path, '/', m.filename)
                END AS featured_url,
                COALESCE(m.alt_text, m.original_name, p.title) AS featured_alt
                FROM posts p
                LEFT JOIN media m ON m.id = p.featured_media_id
                WHERE p.slug = :slug
                  AND p.status = 'published'
                  AND p.is_active = 1
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['slug' => $slug]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row !== false ? $row : null;
    }

    public function metaTitleExists(string $metaTitle, ?int $excludePostId = null): bool
    {
        $sql = "SELECT 1 FROM posts WHERE meta_title = :meta_title";
        $params = ['meta_title' => $metaTitle];

        if ($excludePostId !== null) {
            $sql .= " AND id != :id";
            $params['id'] = $excludePostId;
        }

        $sql .= " LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return (bool)$stmt->fetchColumn();
    }

    public function slugExists(string $slug, ?int $postId = null): bool
    {
        $sql = "SELECT 1 FROM posts WHERE slug = :slug";
        $params = ['slug' => $slug];

        if ($postId !== null) {
            $sql .= " AND id != :id";
            $params['id'] = $postId;
        }

        $sql .= " LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return (bool)$stmt->fetchColumn();
    }

    public function getUniqueSlug(string $baseSlug, ?int $postId = null): string
    {
        $slug = $baseSlug;
        $counter = 2;

        while ($this->slugExists($slug, $postId)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    public function publishPosts(): int
    {
        $sql = "UPDATE posts
                SET status = 'published'
                WHERE status = 'draft'
                  AND to_publish_at IS NOT NULL
                  AND to_publish_at <= NOW()
                  AND is_active = 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->rowCount();
    }

}