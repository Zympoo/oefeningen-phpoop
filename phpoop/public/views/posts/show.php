<?php
declare(strict_types=1);
/*
|--------------------------------------------------------------------------
| View: posts show
|--------------------------------------------------------------------------
| Verwacht:
| - $post (array)
*/
ob_start();
$defaultImg = 'https://images.unsplash.com/photo-1526481280695-3c687fd5432c?auto=format&fit=crop&w=1400&q=60';
$img = (string)($post['featured_url'] ?? '');
if ($img === '') {
    $img = $defaultImg;
}
$alt = (string)($post['featured_alt'] ?? '');
if ($alt === '') {
    $alt = (string)($post['title'] ?? '');
}
?>
    <a href="/posts" class="text-sm text-slate-300 hover:underline">← Terug naar
        overzicht</a>
    <article class="mt-6 rounded-2xl border border-white/10 bg-white/5 overflowhidden">
        <div class="max-h-[460px] w-full bg-black/20">
            <img
                    src="<?= htmlspecialchars($img, ENT_QUOTES) ?>"
                    alt="<?= htmlspecialchars($alt, ENT_QUOTES) ?>"
                    class="w-full max-h-[460px] object-cover object-center"
                    referrerpolicy="no-referrer"
                    onerror="this.onerror=null;this.src='<?=
                    htmlspecialchars($defaultImg, ENT_QUOTES) ?>';"
            >
        </div>
        <div class="p-8">
            <h1 class="text-3xl font-semibold">
                <?= htmlspecialchars((string)($post['title'] ?? ''),
                        ENT_QUOTES) ?>
            </h1>
            <p class="text-sm text-slate-400 mt-3">
                <?= htmlspecialchars((string)($post['created_at'] ?? ''),
                        ENT_QUOTES) ?>
            </p>
            <div class="mt-8 text-slate-200 leading-relaxed">
                <?= nl2br(htmlspecialchars((string)($post['content'] ?? ''),
                        ENT_QUOTES)) ?>
            </div>
        </div>
    </article>
<?php
$content = ob_get_clean();
$title = (string)($post['title'] ?? 'Post');
require __DIR__ . '/../layouts/public.php';