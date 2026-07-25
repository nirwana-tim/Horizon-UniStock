<?php

$isWindows = defined('PHP_WINDOWS_VERSION_BUILD');
$bunAvailable = trim(shell_exec($isWindows ? 'where bun 2>nul' : 'which bun 2>/dev/null'));
$pkg = $bunAvailable ? 'bun' : 'npm';

echo "[" . $pkg . "] Installing frontend dependencies...\n";
passthru($pkg . ' install', $code);
if ($code !== 0) {
    exit($code);
}

echo "[" . $pkg . "] Building assets...\n";
passthru($pkg . ' run build', $code);
exit($code);
