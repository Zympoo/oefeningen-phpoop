<?php
declare(strict_types=1);

use Admin\Core\Auth;
?>

<section class="p-6">
    <div class="bg-white p-6 rounded shadow">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold"><?= $title; ?></h2>

            <?php if (Auth::isAdmin()): ?>
                <a class="underline" href="/admin/users/create">
                    + Nieuwe gebruiker
                </a>
            <?php endif; ?>
        </div>

        <table class="w-full text-sm">
            <thead>
            <tr class="text-left border-b">
                <th class="py-2">Email</th>
                <th>Naam</th>
                <th>Rol</th>
                <th>Status</th>
                <th class="text-right">Acties</th>
            </tr>
            </thead>

            <tbody>
            <?php foreach ($users as $user): ?>
                <tr class="border-b">
                    <td class="py-2"><?php echo htmlspecialchars((string)$user['email'], ENT_QUOTES); ?></td>
                    <td><?php echo htmlspecialchars((string)$user['name'], ENT_QUOTES); ?></td>
                    <td><?php echo htmlspecialchars((string)$user['role_name'], ENT_QUOTES); ?></td>
                    <td><?php echo ((int)$user['is_active'] === 1) ? 'Actief' : 'Geblokkeerd'; ?></td>

                    <td class="text-right">
                        <a class="underline mr-4" href="/admin/users/<?php echo (int)$user['id']; ?>/edit">Bewerk</a>

                        <?php if ((int)$user['is_active'] === 1): ?>
                            <form class="inline" method="post" action="/admin/users/<?php echo (int)$user['id']; ?>/disable">
                                <button class="underline text-red-600" type="submit">Blokkeer</button>
                            </form>
                        <?php else: ?>
                            <form class="inline" method="post" action="/admin/users/<?php echo (int)$user['id']; ?>/enable">
                                <button class="underline text-green-700" type="submit">Deblokkeer</button>
                            </form>
                        <?php endif; ?>
                    </td>

                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
