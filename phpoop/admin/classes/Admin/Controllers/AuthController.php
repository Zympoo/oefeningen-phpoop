<?php
declare(strict_types=1);

namespace Admin\Controllers;

use Admin\Core\View;
use Admin\Core\Auth;
use Admin\Repositories\UsersRepository;

class AuthController
{
    private UsersRepository $usersRepository;

    /**
     * __construct()
     *
     * Doel:
     * Bewaart UsersRepository zodat we users kunnen opzoeken bij login.
     */
    public function __construct(UsersRepository $usersRepository)
    {
        $this->usersRepository = $usersRepository;
    }

    /**
     * showLogin()
     *
     * Doel:
     * Toont de loginpagina met lege errors en old input.
     */
    public function showLogin(): void
    {
        View::render('login.php', [
            'title' => 'Login',
            'errors' => [],
            'old' => [
                'email' => '',
            ],
        ]);
    }

    /**
     * login()
     *
     * Doel:
     * Verwerkt het loginformulier.
     *
     * Werking:
     * 1) Lees email en password uit $_POST.
     * 2) Basis validatie: email/password verplicht.
     * 3) Zoek user op via findByEmail().
     * 4) Als user niet bestaat of password niet klopt -> error.
     * 5) Als login ok -> redirect naar dashboard.
     *
     * Belangrijk:
     * In LES 7.1 maken we nog geen session-login.
     */
    public function login(): void
    {
        $email = trim((string)($_POST['email'] ?? ''));
        $password = (string)($_POST['password'] ?? '');

        $errors = [];

        if ($email === '') {
            $errors[] = 'Email is verplicht.';
        }

        if ($password === '') {
            $errors[] = 'Wachtwoord is verplicht.';
        }

        if (!empty($errors)) {
            View::render('login.php', [
                'title' => 'Login',
                'errors' => $errors,
                'old' => ['email' => $email],
            ]);
            return;
        }

        $user = $this->usersRepository->findByEmail($email);

        if ($user === null) {
            View::render('login.php', [
                'title' => 'Login',
                'errors' => ['Deze login is niet correct.'],
                'old' => ['email' => $email],
            ]);
            return;
        }

        $hash = (string)$user['password_hash'];

        if (!password_verify($password, $hash)) {
            View::render('login.php', [
                'title' => 'Login',
                'errors' => ['Deze login is niet correct.'],
                'old' => ['email' => $email],
            ]);
            return;
        }

        /**
         * Bewaar user_id en role_name zodat we autorisatiechecks kunnen doen.
         */
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['user_role'] = (string)$user['role_name'];

        header('Location: /admin');
        exit;
    }
    /**
     * logout()
     *
     * Doel:
     * Logt de gebruiker uit en stuurt door naar login.
     */
    public function logout(): void
    {
        Auth::logout();

        header('Location: /admin/login');
        exit;
    }

}
