<?php

/**
 * Cross-platform server starter for jquerynativePHPapi
 * Works on both Mac and Windows
 * 
 * Usage: php start.php
 */

$projectRoot = dirname(__FILE__);

echo "Starting jquerynativePHPapi servers...\n";
echo "Project root: $projectRoot\n\n";

// Detect OS
$isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

$backendDir = $projectRoot . '/backend';
$frontendDir = $projectRoot . '/frontend';

echo "✓ Backend running on http://localhost:3001\n";
echo "✓ Frontend running on http://localhost:3000\n";
echo "\n";
echo "Starting servers in separate processes...\n";
echo "Press Ctrl+C to stop\n\n";

// Store process resources
$backendProcess = null;
$frontendProcess = null;

try {
    if ($isWindows) {
        // Windows: use start command to open new windows
        pclose(popen("cd /d " . escapeshellarg($backendDir) . " && start php -S localhost:3001 -t public", "r"));
        sleep(1);
        pclose(popen("cd /d " . escapeshellarg($frontendDir) . " && start php -S localhost:3000", "r"));
    } else {
        // Mac/Linux: use proc_open to keep processes managed
        $backendSpec = array(
            0 => STDIN,
            1 => STDOUT,
            2 => STDERR
        );

        $backendCmd = "cd " . escapeshellarg($backendDir) . " && php -S localhost:3001 -t public";
        $backendProcess = proc_open($backendCmd, $backendSpec, $backendPipes, null, null, array('bypass_shell' => false));

        sleep(1);

        $frontendCmd = "cd " . escapeshellarg($frontendDir) . " && php -S localhost:3000";
        $frontendProcess = proc_open($frontendCmd, $backendSpec, $frontendPipes, null, null, array('bypass_shell' => false));
    }

    // Keep script running
    while (true) {
        sleep(1);
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
} finally {
    // Cleanup on exit
    if ($backendProcess && is_resource($backendProcess)) {
        proc_terminate($backendProcess);
    }
    if ($frontendProcess && is_resource($frontendProcess)) {
        proc_terminate($frontendProcess);
    }
}
