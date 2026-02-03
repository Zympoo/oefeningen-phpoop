<?php
declare(strict_types=1);

spl_autoload_register(function (string $class): void {
    $baseDir = __DIR__ . '/classes/';
    $relativePath = str_replace('\\', '/', $class) . '.php';
    $file = $baseDir . $relativePath;

    if (file_exists($file)) {
        require $file;
    }
});
