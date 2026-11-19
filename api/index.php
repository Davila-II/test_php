<?php
ini_set('display_errors', '1');
error_reporting(E_ALL);

$rootDir = dirname(__DIR__);
$target  = $rootDir . '/index.php';

try {
    if (!is_file($target)) {
        http_response_code(500);
        echo "<h1>index.php racine absent du bundle</h1><pre>";
        echo implode("\n", scandir($rootDir)) . "</pre>";
        exit;
    }
    chdir($rootDir);
    require $target;
} catch (Throwable $e) {
    http_response_code(500);
    echo "<pre>" . htmlspecialchars($e->getMessage() . "\n" . $e->getFile() . ':' . $e->getLine()) . "</pre>";
}
