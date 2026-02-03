<?php
declare(strict_types=1);
?>

<section class="p-6">

    <div class="grid grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded shadow">
            <p class="text-gray-500 text-sm">Posts</p>
            <p class="text-2xl font-bold"><?php echo (int)($stats['posts'] ?? 0); ?></p>
        </div>

        <div class="bg-white p-6 rounded shadow">
            <p class="text-gray-500 text-sm">Users</p>
            <p class="text-2xl font-bold"><?php echo (int)($stats['users'] ?? 0); ?></p>
        </div>

        <div class="bg-white p-6 rounded shadow">
            <p class="text-gray-500 text-sm">Views</p>
            <p class="text-2xl font-bold"><?php echo (int)($stats['views'] ?? 0); ?></p>
        </div>
    </div>

    <div class="bg-white p-6 rounded shadow mb-8">
        <h3 class="font-bold mb-4">Welkom</h3>
        <p class="text-gray-700">Dit is je dashboard. Hier komen later snelle acties en statistieken.</p>
    </div>

</section>
