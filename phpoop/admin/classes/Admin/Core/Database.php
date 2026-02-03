<?php
declare(strict_types=1);

namespace Admin\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $pdo = null;

    /**
     * getConnection()
     *
     * Doel:
     * Geeft één PDO connectie terug die overal hergebruikt wordt.
     *
     * Werking:
     * 1) Bestaat er al een connectie? Return die.
     * 2) Lees database config.
     * 3) Bouw DSN string.
     * 4) Maak PDO object met veilige instellingen.
     * 5) Fout? Toon 500 fout en stop.
     */
    public static function getConnection(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        $config = require __DIR__ . '/../../../config/database.php';

        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            $config['host'],
            $config['dbname'],
            $config['charset']
        );

        try {
            self::$pdo = new PDO(
                $dsn,
                $config['user'],
                $config['pass'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        } catch (PDOException $e) {
            http_response_code(500);
            echo $e . '<h1>500 - Database connectie mislukt</h1>';
            exit;
        }

        return self::$pdo;
    }
}
