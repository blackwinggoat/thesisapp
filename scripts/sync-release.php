<?php

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "This script only runs from the command line.\n");
    exit(1);
}

if ($argc !== 6) {
    fwrite(STDERR, "Usage: sync-release.php SOURCE TARGET EXCLUDES PREVIOUS_MANIFEST OUTPUT_MANIFEST\n");
    exit(1);
}

list(, $sourceRoot, $targetRoot, $excludeFile, $previousManifest, $outputManifest) = $argv;

function failSync($message)
{
    throw new RuntimeException($message);
}

function normalizeRoot($path, $create = false)
{
    if ($create && !is_dir($path) && !mkdir($path, 0700, true)) {
        failSync('Unable to create directory: ' . $path);
    }

    $realPath = realpath($path);

    if ($realPath === false || !is_dir($realPath)) {
        failSync('Directory does not exist: ' . $path);
    }

    return rtrim($realPath, DIRECTORY_SEPARATOR);
}

function loadExcludes($path)
{
    $lines = file($path, FILE_IGNORE_NEW_LINES);

    if ($lines === false) {
        failSync('Unable to read exclusion file: ' . $path);
    }

    return array_values(array_filter(array_map('trim', $lines), function ($line) {
        return $line !== '' && strpos($line, '#') !== 0;
    }));
}

function isExcluded($relativePath, array $patterns)
{
    $relativePath = str_replace('\\', '/', ltrim($relativePath, '/'));

    foreach ($patterns as $pattern) {
        $pattern = str_replace('\\', '/', ltrim($pattern, '/'));

        if (substr($pattern, -1) === '/') {
            $directory = rtrim($pattern, '/');

            if ($relativePath === $directory || strpos($relativePath, $directory . '/') === 0) {
                return true;
            }

            continue;
        }

        if (fnmatch($pattern, $relativePath, FNM_PATHNAME)
            || fnmatch($pattern, basename($relativePath), FNM_PATHNAME)) {
            return true;
        }
    }

    return false;
}

function assertRelativePath($relativePath)
{
    if ($relativePath === '' || $relativePath[0] === '/' || strpos($relativePath, "\0") !== false) {
        failSync('Unsafe relative path: ' . $relativePath);
    }

    foreach (explode('/', str_replace('\\', '/', $relativePath)) as $part) {
        if ($part === '' || $part === '.' || $part === '..') {
            failSync('Unsafe path segment in: ' . $relativePath);
        }
    }
}

function assertSafeLinkTarget($relativePath, $linkTarget)
{
    $base = dirname($relativePath);
    $combined = ($base === '.' ? '' : $base . '/') . $linkTarget;
    $depth = 0;

    foreach (explode('/', $combined) as $part) {
        if ($part === '' || $part === '.') {
            continue;
        }

        if ($part === '..') {
            $depth--;

            if ($depth < 0) {
                failSync('Symbolic link escapes the release root: ' . $relativePath);
            }
        } else {
            $depth++;
        }
    }
}

function collectEntries($root, array $patterns, $relativeDirectory = '')
{
    $entries = [];
    $directory = $relativeDirectory === '' ? $root : $root . '/' . $relativeDirectory;
    $items = scandir($directory);

    if ($items === false) {
        failSync('Unable to read directory: ' . $directory);
    }

    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }

        $relativePath = $relativeDirectory === '' ? $item : $relativeDirectory . '/' . $item;

        if (isExcluded($relativePath, $patterns)) {
            continue;
        }

        assertRelativePath($relativePath);
        $absolutePath = $root . '/' . $relativePath;

        if (is_link($absolutePath)) {
            $linkTarget = readlink($absolutePath);

            if ($linkTarget === false || substr($linkTarget, 0, 1) === '/') {
                failSync('Only relative symbolic links are allowed: ' . $relativePath);
            }

            assertSafeLinkTarget($relativePath, $linkTarget);

            $entries[$relativePath] = [
                'type' => 'link',
                'target' => $linkTarget,
            ];
        } elseif (is_dir($absolutePath)) {
            $entries += collectEntries($root, $patterns, $relativePath);
        } elseif (is_file($absolutePath)) {
            $entries[$relativePath] = [
                'type' => 'file',
                'sha256' => hash_file('sha256', $absolutePath),
                'mode' => fileperms($absolutePath) & 0777,
            ];
        } else {
            failSync('Unsupported filesystem entry: ' . $relativePath);
        }
    }

    ksort($entries);

    return $entries;
}

function writeManifest($path, array $entries)
{
    $directory = dirname($path);

    if (!is_dir($directory) && !mkdir($directory, 0700, true)) {
        failSync('Unable to create manifest directory: ' . $directory);
    }

    $temporaryPath = tempnam($directory, '.manifest-');

    if ($temporaryPath === false) {
        failSync('Unable to create temporary manifest.');
    }

    $payload = json_encode([
        'version' => 1,
        'files' => $entries,
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

    if ($payload === false || file_put_contents($temporaryPath, $payload . "\n", LOCK_EX) === false) {
        @unlink($temporaryPath);
        failSync('Unable to write deployment manifest.');
    }

    chmod($temporaryPath, 0600);

    if (!rename($temporaryPath, $path)) {
        @unlink($temporaryPath);
        failSync('Unable to publish deployment manifest.');
    }
}

function loadManifest($path)
{
    if (!is_file($path)) {
        return [];
    }

    $data = json_decode(file_get_contents($path), true);

    if (!is_array($data) || ($data['version'] ?? null) !== 1 || !is_array($data['files'] ?? null)) {
        failSync('Invalid deployment manifest: ' . $path);
    }

    return $data['files'];
}

function ensureSafeParent($targetRoot, $relativePath)
{
    $parts = explode('/', dirname($relativePath));
    $current = $targetRoot;

    foreach ($parts as $part) {
        if ($part === '.' || $part === '') {
            continue;
        }

        $current .= '/' . $part;

        if (is_link($current)) {
            failSync('Target parent is a symbolic link: ' . $current);
        }

        if (!is_dir($current) && !mkdir($current, 0755)) {
            failSync('Unable to create target directory: ' . $current);
        }
    }
}

function copyEntries($sourceRoot, $targetRoot, array $entries)
{
    foreach ($entries as $relativePath => $metadata) {
        assertRelativePath($relativePath);
        ensureSafeParent($targetRoot, $relativePath);
        $source = $sourceRoot . '/' . $relativePath;
        $target = $targetRoot . '/' . $relativePath;

        if ($metadata['type'] === 'link') {
            if (file_exists($target) || is_link($target)) {
                if (is_dir($target) && !is_link($target)) {
                    failSync('Cannot replace target directory with a link: ' . $relativePath);
                }

                unlink($target);
            }

            if (!symlink($metadata['target'], $target)) {
                failSync('Unable to create symbolic link: ' . $relativePath);
            }

            continue;
        }

        if (is_dir($target) && !is_link($target)) {
            failSync('Cannot replace target directory with a file: ' . $relativePath);
        }

        if (is_file($target) && !is_link($target)
            && hash_file('sha256', $target) === $metadata['sha256']) {
            chmod($target, $metadata['mode']);
            continue;
        }

        $temporaryPath = tempnam(dirname($target), '.deploy-');

        if ($temporaryPath === false || !copy($source, $temporaryPath)) {
            @unlink($temporaryPath);
            failSync('Unable to copy file: ' . $relativePath);
        }

        chmod($temporaryPath, $metadata['mode']);

        if (hash_file('sha256', $temporaryPath) !== $metadata['sha256']) {
            unlink($temporaryPath);
            failSync('Checksum mismatch while copying: ' . $relativePath);
        }

        if (!rename($temporaryPath, $target)) {
            unlink($temporaryPath);
            failSync('Unable to publish file: ' . $relativePath);
        }
    }
}

function deleteStaleEntries($targetRoot, array $previousEntries, array $currentEntries, array $patterns)
{
    $stalePaths = array_diff(array_keys($previousEntries), array_keys($currentEntries));
    usort($stalePaths, function ($left, $right) {
        return strlen($right) <=> strlen($left);
    });

    foreach ($stalePaths as $relativePath) {
        assertRelativePath($relativePath);

        if (isExcluded($relativePath, $patterns)) {
            continue;
        }

        ensureSafeParent($targetRoot, $relativePath);
        $target = $targetRoot . '/' . $relativePath;

        if (is_file($target) || is_link($target)) {
            if (!unlink($target)) {
                failSync('Unable to delete stale file: ' . $relativePath);
            }
        } elseif (is_dir($target)) {
            failSync('Refusing to delete a directory listed as a file: ' . $relativePath);
        }

        $parent = dirname($target);

        while ($parent !== $targetRoot && strpos($parent, $targetRoot . '/') === 0) {
            if (!@rmdir($parent)) {
                break;
            }

            $parent = dirname($parent);
        }
    }
}

try {
    $sourceRoot = normalizeRoot($sourceRoot);
    $targetRoot = normalizeRoot($targetRoot, true);
    $patterns = loadExcludes($excludeFile);
    $previousEntries = loadManifest($previousManifest);
    $currentEntries = collectEntries($sourceRoot, $patterns);

    // Publishing the planned manifest first gives rollback the full list of files
    // that may have been changed if a later copy operation fails.
    writeManifest($outputManifest, $currentEntries);
    copyEntries($sourceRoot, $targetRoot, $currentEntries);
    deleteStaleEntries($targetRoot, $previousEntries, $currentEntries, $patterns);

    printf("SYNC SUCCESS: %d managed files\n", count($currentEntries));
} catch (Throwable $exception) {
    fwrite(STDERR, 'SYNC FAILED: ' . $exception->getMessage() . "\n");
    exit(1);
}
