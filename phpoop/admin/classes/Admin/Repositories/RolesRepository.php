<?php
declare(strict_types=1);

namespace Admin\Repositories;

use Admin\Core\Database;
use PDO;

class RolesRepository
{
    private PDO $pdo;

    /**
     * __construct()
     *
     * Doel:
     * Bewaart PDO zodat we roles kunnen opvragen.
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * getAll()
     *
     * Doel:
     * Haalt alle rollen op (id + name) voor dropdowns en checks.
     */
    public function getAll(): array
    {
        $sql = "SELECT id, name
                FROM roles
                ORDER BY name ASC";

        return $this->pdo->query($sql)->fetchAll();
    }

    /**
     * make()
     *
     * Doel:
     * Factory method om repository snel te maken met standaard connectie.
     */
    public static function make(): self
    {
        return new self(Database::getConnection());
    }
}
