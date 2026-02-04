<?php
declare(strict_types=1);

use Admin\Core\Auth;

?>

<section class="p-6">
    <div class="bg-white p-6 rounded shadow">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold">Posts overzicht</h2>

            <a class="underline" href="/admin/posts/create">
                + Nieuwe post
            </a>
        </div>

        <table class="w-full text-sm">
            <thead>
            <tr class="text-left border-b">
                <th class="py-2">Titel</th>
                <th>Datum</th>
                <th>Status</th>
                <th class="text-right">Acties</th>
            </tr>
            </thead>

            <tbody>
            <?php foreach ($posts as $post): ?>
                <tr class="border-b">
                    <td class="py-2">
                        <a class="underline" href="/admin/posts/<?php echo (int)$post['id']; ?>">
                            <?php echo htmlspecialchars((string)$post['title'], ENT_QUOTES); ?>
                        </a>
                    </td>
                    <td><?php echo htmlspecialchars((string)$post['created_at'], ENT_QUOTES); ?></td>
                    <td><?php echo htmlspecialchars((string)$post['status'], ENT_QUOTES); ?></td>
                    <td class="text-right space-x-3">
                        <a class="underline" href="/admin/posts/<?php echo (int)$post['id']; ?>/edit">
                            Bewerken
                        </a>
                        <?php if (Auth::isAdmin() && (int)$post['is_active'] === 1): ?>
                            <form class="inline" method="post" action="/admin/posts/<?php echo (int)$post['id']; ?>/disable">
                                <button class="underline text-red-600" type="submit">Verwijder</button>
                            </form>
                        <?php else: ?>
                            <form class="inline" method="post" action="/admin/posts/<?php echo (int)$post['id']; ?>/enable">
                                <button class="underline text-green-700" type="submit">Herstel</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
