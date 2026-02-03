<?php
declare(strict_types=1);

namespace Admin\Core;

class Auth
{
    /**
     * check()
     *
     * Doel:
     * Controleert of er een user is ingelogd.
     */
    public static function check(): bool
    {
        return isset($_SESSION['user_id']);
    }

    /**
     * role()
     *
     * Doel:
     * Geeft de rol van de ingelogde user terug.
     */
    public static function role(): ?string
    {
        return $_SESSION['user_role'] ?? null;
    }

    /**
     * isAdmin()
     *
     * Doel:
     * Controleert of de user admin is.
     */
    public static function isAdmin(): bool
    {
        return self::check() && self::role() === 'admin';
    }

    /**
     * logout()
     *
     * Doel:
     * Logt uit en vernietigt session.
     */
    public static function logout(): void
    {
        unset($_SESSION['user_id'], $_SESSION['user_role']);
        session_destroy();
    }
}
