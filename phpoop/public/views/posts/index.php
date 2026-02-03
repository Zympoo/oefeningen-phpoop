<?php
declare(strict_types=1);
/*
|--------------------------------------------------------------------------
| View: posts index
|--------------------------------------------------------------------------
| Verwacht:
| - $posts (array)
*/
ob_start();
$defaultImg = 'https://images.unsplash.com/photo-1526481280695-
3c687fd5432c?auto=format&fit=crop&w=1400&q=60';
?>
    <header class="mb-8">
        <h1 class="text-3xl font-semibold">Posts</h1>
        <p class="text-slate-300 mt-2">Alle gepubliceerde posts.</p>
    </header>
<?php if (empty($posts)): ?>
    <div class="rounded-2xl border border-white/10 bg-white/5 p-6 text-slate300">
        Nog geen gepubliceerde posts.
    </div>
<?php else: ?>
    <div class="grid md:grid-cols-2 gap-6">
        <?php foreach ($posts as $post): ?>
            <?php
            $img = (string)($post['featured_url'] ?? '');
            if ($img === '') {
                $img = $defaultImg;
            }
            $alt = (string)($post['featured_alt'] ?? '');
            if ($alt === '') {
                $alt = (string)($post['title'] ?? '');
            }
            $cleanText = strip_tags((string)($post['content'] ?? ''));
            $preview = mb_strimwidth($cleanText, 0, 220, '...');
            ?>
            <article class="rounded-2xl border border-white/10 bg-white/5
overflow-hidden">
                <a href="/posts/<?= (string)$post['slug'] ?>" class="block">
                    <div class="h-56 w-full bg-black/20">
                        <img
                                src="<?= htmlspecialchars($img, ENT_QUOTES) ?>"
                                alt="<?= htmlspecialchars($alt, ENT_QUOTES) ?>"
                                class="h-56 w-full object-cover object-center"
                                loading="lazy"
                                referrerpolicy="no-referrer"
                                onerror="this.onerror=null;this.src='<?=
                                htmlspecialchars($defaultImg, ENT_QUOTES) ?>';"
                        >
                    </div>
                </a>
                <div class="p-6">
                    <h2 class="text-xl font-semibold">
                        <a class="hover:underline" href="/posts/<?=
                        (string)$post['slug'] ?>">
                            <?= htmlspecialchars((string)($post['title'] ??
                                    ''), ENT_QUOTES) ?>
                        </a>
                    </h2>
                    <p class="text-sm text-slate-400 mt-2">
                        <?= htmlspecialchars((string)($post['created_at'] ??
                                ''), ENT_QUOTES) ?>
                    </p>
                    <p class="text-slate-300 mt-4">
                        <?= htmlspecialchars($preview, ENT_QUOTES) ?>
                    </p>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php
$content = ob_get_clean();
$title = 'Posts';
require __DIR__ . '/../layouts/public.php';