<?php
declare(strict_types=1);

namespace Admin\Repositories;

use Admin\Core\Database;
use PDO;

final class MediaRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public static function make(): self
    {
        return new self(Database::getConnection());
    }

    public function getAllImages(): array
    {
        $sql = "SELECT id, original_name, filename, path, mime_type, size_bytes, alt_text, created_at
                FROM media
                WHERE type = 'image'
                ORDER BY id DESC";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function findImageById(int $id): ?array
    {
        $sql = "SELECT id, original_name, filename, path, mime_type, size_bytes, alt_text, created_at
                FROM media
                WHERE id = :id AND type = 'image'
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row !== false ? $row : null;
    }

    public function createImage(
        string $originalName,
        string $filename,
        string $path,
        string $mimeType,
        int $sizeBytes,
        ?string $altText
    ): int {
        $sql = "INSERT INTO media (type, original_name, filename, path, mime_type, size_bytes, alt_text)
                VALUES ('image', :original_name, :filename, :path, :mime_type, :size_bytes, :alt_text)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'original_name' => $originalName,
            'filename' => $filename,
            'path' => $path,
            'mime_type' => $mimeType,
            'size_bytes' => $sizeBytes,
            'alt_text' => $altText,
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function deleteById(int $id): void
    {
        $sql = "DELETE FROM media WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
    }
}
