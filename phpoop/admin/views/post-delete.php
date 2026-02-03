<?php
declare(strict_types=1);
?>

<section class="p-6">
    <div class="bg-white p-6 rounded shadow max-w-xl">
        <h2 class="text-xl font-bold mb-4 text-red-600">
            <?php echo htmlspecialchars((string)$title, ENT_QUOTES); ?>
        </h2>

        <p class="mb-4">Ben je zeker dat je deze post wil verwijderen?</p>

        <p class="mb-6">
            <strong><?php echo htmlspecialchars((string)$post['title'], ENT_QUOTES); ?></strong>
        </p>

        <form method="post" action="/admin/posts/<?php echo (int)$post['id']; ?>/delete">
            <div class="flex gap-4">
                <button class="border px-4 py-2 text-red-600" type="submit">Ja, verwijder</button>
                <a class="underline" href="/admin/posts">Annuleren</a>
            </div>
        </form>
    </div>
</section>
