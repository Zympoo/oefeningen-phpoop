<?php
declare(strict_types=1);
?>

<section class="p-6">
    <div class="bg-white p-6 rounded shadow">
        <h2 class="text-2xl font-bold mb-2">404 - Pagina niet gevonden</h2>

        <p class="text-gray-700 mb-6">
            De pagina die je zoekt bestaat niet (meer), of je hebt een verkeerde link gevolgd.
        </p>

        <?php if (!empty($requestedUri)): ?>
            <p class="text-sm text-gray-500 mb-6">
                Opgevraagde URL: <span class="font-mono"><?php echo htmlspecialchars((string)$requestedUri, ENT_QUOTES); ?></span>
            </p>
        <?php endif; ?>

        <div class="flex gap-4">
            <a class="underline" href="/admin">Terug naar dashboard</a>
            <a class="underline" href="/admin/posts">Naar posts</a>
        </div>
    </div>
</section>

