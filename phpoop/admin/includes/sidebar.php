<?php
declare(strict_types=1);

$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
$currentPath = rtrim($currentPath, '/') ?: '/';

function isActiveAdmin(string $currentPath, string $targetPath): bool
{
    if ($currentPath === $targetPath) {
        return true;
    }

    if ($targetPath !== ADMIN_BASE_PATH && str_starts_with($currentPath, $targetPath . '/')) {
        return true;
    }

    return false;
}

function sidebarLinkClass(bool $active): string
{
    return $active
            ? 'block rounded px-3 py-2 bg-slate-800 text-white font-semibold'
            : 'block rounded px-3 py-2 text-slate-300 hover:bg-slate-800 hover:text-white';
}
?>

<aside class="w-64 bg-slate-900 text-white p-6">
    <h2 class="text-xl font-bold mb-6">MiniCMS</h2>

    <nav class="space-y-2 text-sm">
        <a href="<?= ADMIN_BASE_PATH ?>"
           class="<?= sidebarLinkClass(isActiveAdmin($currentPath, ADMIN_BASE_PATH)) ?>">
            Dashboard
        </a>

        <a href="<?= ADMIN_BASE_PATH ?>/posts"
           class="<?= sidebarLinkClass(isActiveAdmin($currentPath, ADMIN_BASE_PATH . '/posts')) ?>">
            Posts
        </a>

        <a href="<?= ADMIN_BASE_PATH ?>/media"
           class="<?= sidebarLinkClass(isActiveAdmin($currentPath, ADMIN_BASE_PATH . '/media')) ?>">
            Media
        </a>

        <a href="<?= ADMIN_BASE_PATH ?>/users"
           class="<?= sidebarLinkClass(isActiveAdmin($currentPath, ADMIN_BASE_PATH . '/users')) ?>">
            Users
        </a>

        <hr class="border-slate-700 my-4">

        <a href="/"
           class="block rounded px-3 py-2 text-slate-400 hover:text-white">
            Naar site
        </a>
        <form method="post" action="/admin/logout">
            <button type="submit" class="block rounded px-3 py-2 text-slate-400 hover:text-white">
                Logout
            </button>
        </form>
    </nav>
</aside>