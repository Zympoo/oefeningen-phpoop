<?php
declare(strict_types=1);
?>

<section class="p-6">
    <div class="bg-white p-6 rounded shadow max-w-lg">
        <h2 class="text-xl font-bold mb-4">Nieuwe gebruiker</h2>

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

        <form method="post" action="/admin/users/store" class="space-y-4">
            <div>
                <label class="block text-sm font-bold mb-1" for="email">Email</label>
                <input class="w-full border rounded p-2" type="email" id="email" name="email"
                       value="<?php echo htmlspecialchars((string)($old['email'] ?? ''), ENT_QUOTES); ?>">
            </div>

            <div>
                <label class="block text-sm font-bold mb-1" for="name">Naam</label>
                <input class="w-full border rounded p-2" type="text" id="name" name="name"
                       value="<?php echo htmlspecialchars((string)($old['name'] ?? ''), ENT_QUOTES); ?>">
            </div>

            <div>
                <label class="block text-sm font-bold mb-1" for="password">Wachtwoord</label>
                <input class="w-full border rounded p-2" type="password" id="password" name="password">
            </div>

            <div>
                <label class="block text-sm font-bold mb-1" for="role_id">Rol</label>
                <select class="w-full border rounded p-2" id="role_id" name="role_id">
                    <option value="">Kies een rol</option>
                    <?php foreach ($roles as $role): ?>
                        <option
                            value="<?php echo (int)$role['id']; ?>"
                            <?php echo ((string)($old['role_id'] ?? '') === (string)$role['id']) ? 'selected' : ''; ?>
                        >
                            <?php echo htmlspecialchars((string)$role['name'], ENT_QUOTES); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex gap-4">
                <button class="border rounded px-4 py-2" type="submit">Aanmaken</button>
                <a class="underline" href="/admin/users">Annuleren</a>
            </div>
        </form>
    </div>
</section>
