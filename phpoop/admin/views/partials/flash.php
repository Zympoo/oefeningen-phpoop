<?php
declare(strict_types=1);

use Admin\Core\Flash;

$success = Flash::get('success'); // string|null
$error   = Flash::get('error');   // string|null
$warning = Flash::get('warning'); // array|string|null

$warningList = null;
if (is_array($warning)) {
    $warningList = $warning;
} elseif (is_string($warning) && $warning !== '') {
    $warningList = [$warning];
}
?>

<?php if (is_string($success) && $success !== ''): ?>
    <div class="mx-6 mt-6 rounded border border-green-200 bg-green-50 px-4 py-3 text-green-800">
        <?php echo htmlspecialchars($success, ENT_QUOTES); ?>
    </div>
<?php endif; ?>

<?php if (is_string($error) && $error !== ''): ?>
    <div class="mx-6 mt-6 rounded border border-red-200 bg-red-50 px-4 py-3 text-red-800">
        <?php echo htmlspecialchars($error, ENT_QUOTES); ?>
    </div>
<?php endif; ?>

<?php if (is_array($warningList) && !empty($warningList)): ?>
    <div class="mx-6 mt-6 rounded border border-yellow-200 bg-yellow-50 px-4 py-3 text-yellow-900">
        <p class="font-bold mb-2">Controleer je invoer:</p>
        <ul class="list-disc pl-6">
            <?php foreach ($warningList as $msg): ?>
                <li><?php echo htmlspecialchars((string)$msg, ENT_QUOTES); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
