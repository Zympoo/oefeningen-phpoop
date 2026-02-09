<?php
declare(strict_types=1);

use Admin\Core\Auth;

?>

<section class="p-6">
    <div class="bg-white p-6 rounded shadow">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold">Revisies van post: <?php echo htmlspecialchars((string)$post['title'], ENT_QUOTES); ?></h2>
            <a class="underline" href="/admin/posts">
                ← Terug naar posts
            </a>
        </div>

        <?php if (empty($revisions)): ?>
            <p>Geen revisies beschikbaar voor deze post.</p>
        <?php else: ?>
            <table class="w-full text-sm">
                <thead>
                <tr class="text-left border-b">
                    <th class="py-2">Titel</th>
                    <th>Datum revisie</th>
                    <th>Status</th>
                    <th class="text-right">Acties</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($revisions as $rev): ?>
                    <tr class="border-b">
                        <td class="py-2">
                            <a class="underline" href="/admin/posts/<?php echo (int)$post['id']; ?>/revisions/<?php echo (int)$rev['id']; ?>">
                                <?php echo htmlspecialchars((string)$rev['title'], ENT_QUOTES); ?>
                            </a>
                        </td>
                        <td><?php echo htmlspecialchars((string)$rev['created_at'], ENT_QUOTES); ?></td>
                        <td><?php echo htmlspecialchars((string)$rev['status'], ENT_QUOTES); ?></td>
                        <td class="text-right">
                            <?php if (Auth::isAdmin()): ?>
                                <form class="inline" method="post" action="/admin/posts/<?php echo (int)$post['id']; ?>/revisions/<?php echo (int)$rev['id']; ?>/restore">
                                    <button class="underline text-blue-600" type="submit">Herstellen</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</section>