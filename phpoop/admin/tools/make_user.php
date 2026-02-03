<?php
declare(strict_types=1);

/**
 * Dit script genereert een password hash voor een nieuw wachtwoord.
 * Je gebruikt de output om een user in de database te inserten.
 */

$password = 'admin123';
echo password_hash($password, PASSWORD_DEFAULT);

