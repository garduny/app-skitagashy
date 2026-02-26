<?php
if (php_sapi_name() !== 'cli') exit;
$exclude = [
    'build-app.php',
    'vite.config.js',
    'postcss.config.js',
    'package.json',
    'package-lock.json',
    '.gitignore',
    'node_modules',
    'dev',
    '.git'
];
$root = realpath(__DIR__);
$cleanDirs = [
    $root . '/.cache',
    $root . '/.cache/queries'
];
foreach ($cleanDirs as $dir) {
    if (!is_dir($dir)) continue;
    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($it as $f) {
        $keep =
            ($f->getFilename() === '.htaccess') &&
            (
                realpath($f->getPath()) === realpath($root . '/.cache') ||
                realpath($f->getPath()) === realpath($root . '/.cache/queries')
            );
        if ($keep) continue;
        $f->isDir() ? @rmdir($f->getRealPath()) : @unlink($f->getRealPath());
    }
}
$zipPath = $root . '/built-project-' . date('Y-m-d_H-i-s') . '.zip';
$zip = new ZipArchive();
$zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

$it = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);
foreach ($it as $f) {
    $p = $f->getRealPath();
    if (!$p || $p === $zipPath) continue;
    $r = ltrim(str_replace($root, '', $p), DIRECTORY_SEPARATOR);
    $r = str_replace(DIRECTORY_SEPARATOR, '/', $r);
    $first = strtok($r, '/');
    if (in_array($r, $exclude, true) || in_array($first, $exclude, true)) continue;
    $f->isDir() ? $zip->addEmptyDir($r) : $zip->addFile($p, $r);
}
$zip->close();
echo $zipPath . PHP_EOL;
