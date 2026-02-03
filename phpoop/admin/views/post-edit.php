<?php
declare(strict_types=1);
?>

<section class="p-6">
    <div class="bg-white p-6 rounded shadow max-w-2xl">
        <h1 class="text-2xl font-bold mb-4"><?= htmlspecialchars((string)($title ?? 'Post bewerken'), ENT_QUOTES) ?></h1>

        <?php require __DIR__ . '/partials/flash.php'; ?>

        <form method="post" action="<?= ADMIN_BASE_PATH ?>/posts/<?= (int)($postId ?? 0) ?>/update" class="space-y-4">
            <div>
                <label class="block text-sm font-semibold mb-1">Titel</label>
                <input class="w-full border rounded px-3 py-2"
                       type="text"
                       name="title"
                       value="<?= htmlspecialchars((string)($old['title'] ?? ''), ENT_QUOTES) ?>"
                       required>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1">Inhoud</label>
                <textarea class="w-full border rounded px-3 py-2"
                          name="content"
                          rows="10"
                          required><?= htmlspecialchars((string)($old['content'] ?? ''), ENT_QUOTES) ?></textarea>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1">Status</label>
                <?php $status = (string)($old['status'] ?? 'draft'); ?>
                <select class="w-full border rounded px-3 py-2" name="status">
                    <option value="draft" <?= $status === 'draft' ? 'selected' : '' ?>>draft</option>
                    <option value="published" <?= $status === 'published' ? 'selected' : '' ?>>published</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1">Featured image</label>
                <?php $featured = (string)($old['featured_media_id'] ?? ''); ?>
                <select class="w-full border rounded px-3 py-2" name="featured_media_id">
                    <option value="">Geen</option>
                    <?php foreach (($media ?? []) as $item): ?>
                        <option value="<?= (int)$item['id'] ?>" <?= ((string)$item['id'] === $featured) ? 'selected' : '' ?>>
                            <?= htmlspecialchars((string)$item['original_name'], ENT_QUOTES) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex gap-3">
                <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700" type="submit">
                    Opslaan
                </button>
                <a class="px-4 py-2 rounded border" href="<?= ADMIN_BASE_PATH ?>/posts">Annuleren</a>
            </div>
        </form>
    </div>
</section>
