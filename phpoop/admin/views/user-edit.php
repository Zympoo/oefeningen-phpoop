<?php
declare(strict_types=1);

$email = (string)($user['email'] ?? '');
$nameValue = (string)($old['name'] ?? ($user['name'] ?? ''));
$roleIdValue = (string)($old['role_id'] ?? ($user['role_id'] ?? ''));
$isActive = ((int)($user['is_active'] ?? 0) === 1);
?>

<section class="p-6">
    <div class="bg-white p-6 rounded shadow max-w-2xl">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold">Gebruiker bewerken</h2>
            <a class="underline" href="/admin/users">Terug naar overzicht</a>
        </div>

        <div class="mb-6 text-sm space-y-1">
            <p><strong>Email:</strong> <?php echo htmlspecialchars($email, ENT_QUOTES); ?></p>
            <p><strong>Status:</strong> <?php echo $isActive ? 'Actief' : 'Geblokkeerd'; ?></p>
            <p><strong>Huidige rol:</strong> <?php echo htmlspecialchars((string)($user['role_name'] ?? ''), ENT_QUOTES); ?></p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="mb-4 p-4 border border-red-200 bg-red-50 rounded">
                <p class="font-bold mb-2">Controleer je invoer:</p>
                <ul class="list-disc pl-6">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars((string)$error, ENT_QUOTES); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="/admin/users/<?php echo (int)$user['id']; ?>/update" class="space-y-4">
            <div>
                <label class="block text-sm font-bold mb-1" for="email">Email (readonly)</label>
                <input class="w-full border rounded p-2 bg-gray-100" type="email" id="email"
                       value="<?php echo htmlspecialchars($email, ENT_QUOTES); ?>" disabled>
            </div>

            <div>
                <label class="block text-sm font-bold mb-1" for="name">Naam</label>
                <input class="w-full border rounded p-2" type="text" id="name" name="name"
                       value="<?php echo htmlspecialchars($nameValue, ENT_QUOTES); ?>">
            </div>

            <div>
                <label class="block text-sm font-bold mb-1" for="role_id">Rol</label>
                <select class="w-full border rounded p-2" id="role_id" name="role_id">
                    <?php foreach ($roles as $role): ?>
                        <option
                            value="<?php echo (int)$role['id']; ?>"
                            <?php echo ($roleIdValue === (string)$role['id']) ? 'selected' : ''; ?>
                        >
                            <?php echo htmlspecialchars((string)$role['name'], ENT_QUOTES); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex gap-4">
                <button class="border rounded px-4 py-2" type="submit">Opslaan</button>
                <a class="underline" href="/admin/users">Annuleren</a>
            </div>
        </form>

        <hr class="my-8">

        <h3 class="text-lg font-bold mb-3">Wachtwoord resetten</h3>

        <?php if (!empty($pw_errors)): ?>
            <div class="mb-4 p-4 border border-red-200 bg-red-50 rounded">
                <p class="font-bold mb-2">Controleer je invoer:</p>
                <ul class="list-disc pl-6">
                    <?php foreach ($pw_errors as $error): ?>
                        <li><?php echo htmlspecialchars((string)$error, ENT_QUOTES); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="/admin/users/<?php echo (int)$user['id']; ?>/reset-password" class="space-y-4">
            <div>
                <label class="block text-sm font-bold mb-1" for="password">Nieuw wachtwoord</label>
                <input class="w-full border rounded p-2" type="password" id="password" name="password" autocomplete="new-password">
                <p class="text-xs text-gray-600 mt-1">Minstens 8 tekens.</p>
            </div>

            <div>
                <label class="block text-sm font-bold mb-1" for="password_confirm">Bevestig wachtwoord</label>
                <input class="w-full border rounded p-2" type="password" id="password_confirm" name="password_confirm" autocomplete="new-password">
            </div>

            <div>
                <button class="border rounded px-4 py-2" type="submit">Reset wachtwoord</button>
            </div>
        </form>

        <hr class="my-8">

        <h3 class="text-lg font-bold mb-3">Status beheren</h3>

        <?php if ($isActive): ?>
            <form method="post" action="/admin/users/<?php echo (int)$user['id']; ?>/disable">
                <button class="underline text-red-600" type="submit">Blokkeer gebruiker</button>
            </form>
        <?php else: ?>
            <form method="post" action="/admin/users/<?php echo (int)$user['id']; ?>/enable">
                <button class="underline text-green-700" type="submit">Deblokkeer gebruiker</button>
            </form>
        <?php endif; ?>
    </div>
</section>
