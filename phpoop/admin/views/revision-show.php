<?php
declare(strict_types=1);

use Admin\Core\Auth;

?>

<section class="p-6">
    <div class="bg-white p-6 rounded shadow max-w-3xl">
        <h2 class="text-2xl font-bold mb-4">
            Revisie van: <?php echo htmlspecialchars((string)$post['title'], ENT_QUOTES); ?>
        </h2>

        <p class="mb-6 whitespace-pre-wrap">
            <?php echo htmlspecialchars((string)$revision['content'], ENT_QUOTES); ?>
        </p>

        <div class="text-sm text-gray-600">
            <span class="mr-4">Status: <?php echo htmlspecialchars((string)$revision['status'], ENT_QUOTES); ?></span>
            <span>Gemaakt op: <?php echo htmlspecialchars((string)$revision['created_at'], ENT_QUOTES); ?></span>
        </div>

        <div class="flex gap-10 mt-6">
            <a class="underline" href="/admin/posts/<?php echo (int)$post['id']; ?>/revisions">
                Terug naar revisies
            </a>

            <?php if(Auth::isAdmin()): ?>
                <form class="inline" method="post" action="/admin/posts/<?php echo (int)$post['id']; ?>/revisions/<?php echo (int)$revision['id']; ?>/restore">
                    <button class="underline text-blue-600" type="submit">
                        Herstellen
                    </button>
                </form>
            <?php endif ?>
        </div>
    </div>
</section>