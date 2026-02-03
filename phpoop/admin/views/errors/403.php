<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| View: errors/403
|--------------------------------------------------------------------------
| Verwacht:
| - $title (string)
| - $message (string|null)
*/
?>

<section class="p-6">
    <div class="bg-white p-6 rounded shadow max-w-xl">
        <h1 class="text-xl font-bold text-red-600 mb-2">
            <?= htmlspecialchars($title ?? '403 - Geen toegang') ?>
        </h1>

        <p class="mb-4">
            <?= htmlspecialchars($message ?? 'Je hebt geen rechten om deze pagina te bekijken.') ?>
        </p>

        <a class="inline-block mt-2 text-blue-600 hover:underline"
           href="<?= ADMIN_BASE_PATH ?>">
            Terug naar dashboard
        </a>
    </div>
</section>
