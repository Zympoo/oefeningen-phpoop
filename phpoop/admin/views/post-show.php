<?php
declare(strict_types=1);

use Admin\Core\Auth;

?>

<section class="p-6">
    <div class="bg-white p-6 rounded shadow max-w-3xl">
        <h2 class="text-2xl font-bold mb-4">
            <?php echo htmlspecialchars((string)$post['title'], ENT_QUOTES); ?>
        </h2>

        <p class="mb-6 whitespace-pre-wrap">
            <?php echo htmlspecialchars((string)$post['content'], ENT_QUOTES); ?>
        </p>

        <div class="text-sm text-gray-600">
            <span class="mr-4">Status: <?php echo htmlspecialchars((string)$post['status'], ENT_QUOTES); ?></span>
            <span>Datum: <?php echo htmlspecialchars((string)$post['created_at'], ENT_QUOTES); ?></span>
        </div>

        <div class="flex gap-10 mt-6">
            <a class="underline" href="/admin/posts">Terug naar overzicht</a>
            <?php if(Auth::isAdmin()): ?>
            <a class="underline text-red-600" href="/admin/posts/<?php echo (int)$post['id']; ?>/delete">
                Definitief verwijderen
            </a>
            <?php endif ?>
        </div>
    </div>
</section>
