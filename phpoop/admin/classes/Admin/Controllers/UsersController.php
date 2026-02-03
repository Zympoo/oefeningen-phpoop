<?php
declare(strict_types=1);

namespace Admin\Controllers;

use Admin\Core\Auth;
use Admin\Core\Flash;
use Admin\Core\View;
use Admin\Repositories\RolesRepository;
use Admin\Repositories\UsersRepository;

class UsersController
{
    private UsersRepository $users;
    private RolesRepository $roles;

    public function __construct(UsersRepository $users, RolesRepository $roles)
    {
        $this->users = $users;
        $this->roles = $roles;
    }

    public function index(): void
    {
        if (!Auth::isAdmin()) {
            header('Location: /admin');
            exit;
        }

        View::render('users.php', [
            'title' => 'Gebruikers',
            'users' => $this->users->getAll(),
        ]);
    }

    public function create(): void
    {
        if (!Auth::isAdmin()) {
            header('Location: /admin');
            exit;
        }

        View::render('user-create.php', [
            'title' => 'Nieuwe gebruiker',
            'roles' => $this->roles->getAll(),
            'errors' => [],
            'old' => [
                'email' => '',
                'name' => '',
                'role_id' => '',
            ],
        ]);
    }

    public function store(): void
    {
        if (!Auth::isAdmin()) {
            header('Location: /admin');
            exit;
        }

        $email = trim((string)($_POST['email'] ?? ''));
        $name = trim((string)($_POST['name'] ?? ''));
        $password = (string)($_POST['password'] ?? '');
        $roleId = (int)($_POST['role_id'] ?? 0);

        $errors = [];

        if ($email === '') { $errors[] = 'Email is verplicht.'; }
        if ($name === '') { $errors[] = 'Naam is verplicht.'; }
        if ($password === '') { $errors[] = 'Wachtwoord is verplicht.'; }
        if ($roleId <= 0) { $errors[] = 'Kies een rol.'; }

        if (!empty($errors)) {
            View::render('user-create.php', [
                'title' => 'Nieuwe gebruiker',
                'roles' => $this->roles->getAll(),
                'errors' => $errors,
                'old' => [
                    'email' => $email,
                    'name' => $name,
                    'role_id' => (string)$roleId,
                ],
            ]);
            return;
        }

        $this->users->create($email, $name, $password, $roleId);

        Flash::set('Gebruiker aangemaakt.', 'success');
        header('Location: /admin/users');
        exit;
    }

    public function edit(int $id): void
    {
        if (!Auth::isAdmin()) {
            header('Location: /admin');
            exit;
        }

        $user = $this->users->findById($id);
        if ($user === null) {
            Flash::set('Gebruiker niet gevonden.', 'error');
            header('Location: /admin/users');
            exit;
        }

        View::render('user-edit.php', [
            'title' => 'Gebruiker bewerken',
            'user' => $user,
            'roles' => $this->roles->getAll(),
            'errors' => [],
            'old' => [
                'name' => (string)$user['name'],
                'role_id' => (string)$user['role_id'],
            ],
            'pw_errors' => [],
        ]);
    }

    public function update(int $id): void
    {
        if (!Auth::isAdmin()) {
            header('Location: /admin');
            exit;
        }

        $user = $this->users->findById($id);
        if ($user === null) {
            Flash::set('Gebruiker niet gevonden.', 'error');
            header('Location: /admin/users');
            exit;
        }

        $name = trim((string)($_POST['name'] ?? ''));
        $roleId = (int)($_POST['role_id'] ?? 0);

        $errors = [];

        if ($name === '') { $errors[] = 'Naam is verplicht.'; }
        if ($roleId <= 0) { $errors[] = 'Kies een rol.'; }

        if (!empty($errors)) {
            View::render('user-edit.php', [
                'title' => 'Gebruiker bewerken',
                'user' => $user,
                'roles' => $this->roles->getAll(),
                'errors' => $errors,
                'old' => [
                    'name' => $name,
                    'role_id' => (string)$roleId,
                ],
                'pw_errors' => [],
            ]);
            return;
        }

        $this->users->update($id, $name, $roleId);

        Flash::set('Gebruiker bijgewerkt.', 'success');
        header('Location: /admin/users/' . $id . '/edit');
        exit;
    }

    public function resetPassword(int $id): void
    {
        if (!Auth::isAdmin()) {
            header('Location: /admin');
            exit;
        }

        $user = $this->users->findById($id);
        if ($user === null) {
            Flash::set('Gebruiker niet gevonden.', 'error');
            header('Location: /admin/users');
            exit;
        }

        $password = (string)($_POST['password'] ?? '');
        $confirm = (string)($_POST['password_confirm'] ?? '');

        $pwErrors = [];

        if ($password === '') { $pwErrors[] = 'Wachtwoord is verplicht.'; }
        if (strlen($password) < 8) { $pwErrors[] = 'Wachtwoord moet minstens 8 tekens zijn.'; }
        if ($password !== $confirm) { $pwErrors[] = 'Wachtwoorden komen niet overeen.'; }

        if (!empty($pwErrors)) {
            View::render('user-edit.php', [
                'title' => 'Gebruiker bewerken',
                'user' => $user,
                'roles' => $this->roles->getAll(),
                'errors' => [],
                'old' => [
                    'name' => (string)$user['name'],
                    'role_id' => (string)$user['role_id'],
                ],
                'pw_errors' => $pwErrors,
            ]);
            return;
        }

        $this->users->updatePassword($id, $password);

        Flash::set('Wachtwoord gereset.', 'success');
        header('Location: /admin/users/' . $id . '/edit');
        exit;
    }

    public function disable(int $id): void
    {
        if (!Auth::isAdmin()) {
            header('Location: /admin');
            exit;
        }

        $this->users->disable($id);

        Flash::set('Gebruiker geblokkeerd.', 'success');
        header('Location: /admin/users/' . $id . '/edit');
        exit;
    }

    public function enable(int $id): void
    {
        if (!Auth::isAdmin()) {
            header('Location: /admin');
            exit;
        }

        $this->users->enable($id);

        Flash::set('Gebruiker geactiveerd.', 'success');
        header('Location: /admin/users/' . $id . '/edit');
        exit;
    }
}
