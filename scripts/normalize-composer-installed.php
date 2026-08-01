<?php

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "This script only runs from the command line.\n");
    exit(1);
}

$installedPath = $argv[1] ?? dirname(__DIR__).'/vendor/composer/installed.json';

if (!is_file($installedPath)) {
    fwrite(STDERR, "Composer installed metadata is missing: {$installedPath}\n");
    exit(1);
}

$contents = file_get_contents($installedPath);
$installed = json_decode($contents, true);

if (!is_array($installed) || json_last_error() !== JSON_ERROR_NONE) {
    fwrite(STDERR, "Composer installed metadata is invalid JSON: {$installedPath}\n");
    exit(1);
}

if (!array_key_exists('packages', $installed)) {
    exit(0);
}

if (!is_array($installed['packages'])) {
    fwrite(STDERR, "Composer installed metadata has an invalid packages list.\n");
    exit(1);
}

$normalized = json_encode($installed['packages'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

if ($normalized === false) {
    fwrite(STDERR, "Unable to encode normalized Composer metadata.\n");
    exit(1);
}

$directory = dirname($installedPath);
$temporaryPath = tempnam($directory, '.installed-');

if ($temporaryPath === false
    || file_put_contents($temporaryPath, $normalized."\n", LOCK_EX) === false
    || !chmod($temporaryPath, fileperms($installedPath) & 0777)
    || !rename($temporaryPath, $installedPath)) {
    if ($temporaryPath !== false) {
        @unlink($temporaryPath);
    }

    fwrite(STDERR, "Unable to publish normalized Composer metadata.\n");
    exit(1);
}

fwrite(STDOUT, "Composer installed metadata normalized for Laravel 5.6.\n");
