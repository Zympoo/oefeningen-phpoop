<?php
declare(strict_types=1);
?>

<section class="p-6">
    <div class="bg-white p-6 rounded shadow max-w-xl">
        <h1 class="text-2xl font-bold mb-4"><?= htmlspecialchars((string)($title ?? 'Upload afbeelding'), ENT_QUOTES) ?></h1>

        <?php require __DIR__ . '/partials/flash.php'; ?>

        <form method="post"
              action="<?= ADMIN_BASE_PATH ?>/media/store"
              enctype="multipart/form-data"
              class="space-y-4">

            <div>
                <label class="block text-sm font-semibold mb-1">Afbeelding (JPG/PNG/WEBP, max 5MB)</label>
                <input type="file"
                       name="image"
                       accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                       required>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1">Alt-tekst (optioneel)</label>
                <input type="text"
                       name="alt_text"
                       value="<?= htmlspecialchars((string)($old['alt_text'] ?? ''), ENT_QUOTES) ?>"
                       class="w-full border rounded px-3 py-2"
                       placeholder="Beschrijving van de afbeelding">
            </div>

            <div class="flex gap-3">
                <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700" type="submit">
                    Upload
                </button>
                <a class="px-4 py-2 rounded border" href="<?= ADMIN_BASE_PATH ?>/media">Annuleren</a>
            </div>
        </form>
    </div>
</section>
