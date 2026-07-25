<?php

$isWindows = defined('PHP_WINDOWS_VERSION_BUILD');
$bunAvailable = trim(shell_exec($isWindows ? 'where bun 2>nul' : 'which bun 2>/dev/null'));
$npx = $bunAvailable ? 'bunx' : 'npx';
$pkg = $bunAvailable ? 'bun' : 'npm';

$cmd = $npx . ' concurrently -c "#93c5fd,#c4b5fd,#fb7185,#fdba74"'
    . ' "php artisan serve"'
    . ' "php artisan queue:listen --tries=1 --timeout=0"'
    . ' "php artisan pail --timeout=0"'
    . ' "' . $pkg . ' run dev"'
    . ' --names=server,queue,logs,vite --kill-others';

passthru($cmd, $exitCode);
exit($exitCode);
