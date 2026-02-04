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

    public function redirectToGitHub(): void
    {
        $state = bin2hex(random_bytes(16));
        $_SESSION['oauth_state'] = $state;

        $url = 'https://github.com/login/oauth/authorize?' . http_build_query([
                'client_id' => GITHUB_CLIENT_ID,
                'redirect_uri' => GITHUB_REDIRECT_URI,
                'scope' => 'user:email',
                'state' => $state,
            ]);

        header('Location: ' . $url);
        exit;
    }

    public function githubCallback(): void
    {
        if (
            !isset($_GET['state'], $_SESSION['oauth_state']) ||
            $_GET['state'] !== $_SESSION['oauth_state']
        ) {
            http_response_code(403);
            exit('Invalid state');
        }
        unset($_SESSION['oauth_state']);

        $code = $_GET['code'] ?? null;
        if ($code === null) {
            exit('No code received');
        }

        // Access token ophalen
        $accessToken = $this->fetchGitHubAccessToken($code);

        // User info ophalen
        $externalUser = $this->fetchGitHubUser($accessToken);

        // Koppelen / aanmaken + session login
        $this->loginOrCreateUser('github', $externalUser);
    }

    private function fetchGitHubAccessToken(string $code): string
    {
        $response = file_get_contents('https://github.com/login/oauth/access_token', false, stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Accept: application/json\r\n",
                'content' => http_build_query([
                    'client_id' => GITHUB_CLIENT_ID,
                    'client_secret' => GITHUB_CLIENT_SECRET,
                    'code' => $code,
                    'redirect_uri' => GITHUB_REDIRECT_URI,
                ]),
            ],
        ]));

        $data = json_decode($response, true);

        return $data['access_token'];
    }

    private function fetchGitHubUser(string $token): array
    {
        $context = stream_context_create([
            'http' => [
                'header' => "Authorization: token {$token}\r\nUser-Agent: MiniCMS\r\n",
            ],
        ]);

        $response = file_get_contents('https://api.github.com/user', false, $context);
        $user = json_decode($response, true);

        // Email ophalen (GitHub geeft soms null)
        if (empty($user['email'])) {
            $emails = file_get_contents('https://api.github.com/user/emails', false, $context);
            $emails = json_decode($emails, true);
            $primary = array_filter($emails, fn($e) => $e['primary'] && $e['verified']);
            $user['email'] = $primary ? $primary[array_key_first((array)$primary)]['email'] : '';
        }

        return [
            'id' => (string)$user['id'],
            'email' => $user['email'],
            'name' => $user['name'] ?? $user['login'],
        ];
    }

    private function loginOrCreateUser(string $provider, array $externalUser): void
    {
        $user = $this->usersRepository->findByProvider($provider, $externalUser['id']);

        if ($user === null) {
            $userId = $this->usersRepository->createExternal(
                $externalUser['email'],
                $externalUser['name'],
                $provider,
                $externalUser['id']
            );
            $user = $this->usersRepository->findById($userId);
        }

        if ((int)$user['is_active'] !== 1) {
            exit('Account is inactive');
        }

        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['user_role'] = (string)$user['role_name'];

        header('Location: /admin');
        exit;
    }
}
