<?php
declare(strict_types=1);
?>

<section class="p-6">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold"><?= htmlspecialchars((string)($title ?? 'Media'), ENT_QUOTES) ?></h1>

        <a href="<?= ADMIN_BASE_PATH ?>/media/upload"
           class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Upload afbeelding
        </a>
    </div>

    <?php require __DIR__ . '/partials/flash.php'; ?>

    <?php if (empty($items)): ?>
        <div class="bg-white p-6 rounded shadow">
            <p class="text-gray-700">Nog geen afbeeldingen geüpload.</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <?php foreach ($items as $item): ?>
                <div class="bg-white rounded shadow overflow-hidden">
                    <img
                        src="/<?= htmlspecialchars((string)$item['path'], ENT_QUOTES) ?>/<?= htmlspecialchars((string)$item['filename'], ENT_QUOTES) ?>"
                        alt="<?= htmlspecialchars((string)($item['alt_text'] ?? $item['original_name']), ENT_QUOTES) ?>"
                        class="w-full h-40 object-cover"
                    >

                    <div class="p-3">
                        <p class="text-sm font-semibold truncate">
                            <?= htmlspecialchars((string)$item['original_name'], ENT_QUOTES) ?>
                        </p>

                        <form method="post"
                              action="<?= ADMIN_BASE_PATH ?>/media/<?= (int)$item['id'] ?>/delete"
                              onsubmit="return confirm('Ben je zeker dat je dit item wil verwijderen?');">
                            <button type="submit" class="mt-3 text-sm text-red-600 hover:underline">
                                Verwijderen
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
