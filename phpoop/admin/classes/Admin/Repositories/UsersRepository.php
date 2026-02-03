<?php
declare(strict_types=1);

namespace Admin\Repositories;

use Admin\Core\Database;
use PDO;

class UsersRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * getAll()
     * Doel: admin-overzicht van alle users + rolnaam.
     */
    public function getAll(): array
    {
        $sql = "SELECT u.id, u.email, u.name, u.is_active, r.name AS role_name
                FROM users u
                JOIN roles r ON r.id = u.role_id
                ORDER BY u.id ASC";

        return $this->pdo->query($sql)->fetchAll();
    }

    /**
     * findByEmail()
     * Doel: login alleen voor actieve users.
     */
    public function findByEmail(string $email): ?array
    {
        $sql = "SELECT u.id, u.email, u.password_hash, u.name, u.is_active, r.name AS role_name
                FROM users u
                JOIN roles r ON r.id = u.role_id
                WHERE u.email = :email
                AND u.is_active = 1
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['email' => $email]);

        $user = $stmt->fetch();

        return $user === false ? null : $user;
    }

    /**
     * create()
     * Doel: nieuwe user aanmaken met hash en default actief.
     */
    public function create(string $email, string $name, string $plainPassword, int $roleId): void
    {
        $sql = "INSERT INTO users (email, name, password_hash, role_id, is_active)
                VALUES (:email, :name, :hash, :role_id, 1)";

        $hash = password_hash($plainPassword, PASSWORD_DEFAULT);

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'email' => $email,
            'name' => $name,
            'hash' => $hash,
            'role_id' => $roleId,
        ]);
    }

    /**
     * findById()
     * Doel: user ophalen voor edit-form, inclusief role_id.
     */
    public function findById(int $id): ?array
    {
        $sql = "SELECT u.id, u.email, u.name, u.role_id, u.is_active, r.name AS role_name
                FROM users u
                JOIN roles r ON r.id = u.role_id
                WHERE u.id = :id
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        $user = $stmt->fetch();

        return $user === false ? null : $user;
    }

    /**
     * update()
     * Doel: naam + rol wijzigen.
     */
    public function update(int $id, string $name, int $roleId): void
    {
        $sql = "UPDATE users
                SET name = :name,
                    role_id = :role_id
                WHERE id = :id
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'name' => $name,
            'role_id' => $roleId,
        ]);
    }

    /**
     * updatePassword()
     * Doel: wachtwoord resetten (hash vervangen).
     */
    public function updatePassword(int $id, string $plainPassword): void
    {
        $sql = "UPDATE users
                SET password_hash = :hash
                WHERE id = :id
                LIMIT 1";

        $hash = password_hash($plainPassword, PASSWORD_DEFAULT);

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'hash' => $hash,
        ]);
    }

    /**
     * disable()
     * Doel: user blokkeren.
     */
    public function disable(int $id): void
    {
        $sql = "UPDATE users
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
        $sql = "UPDATE users
                SET is_active = 1
                WHERE id = :id
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
    }

    public static function make(): self
    {
        return new self(Database::getConnection());
    }
}
