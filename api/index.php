<?php
ini_set('display_errors', '0');
error_reporting(E_ALL);

$rootDir = dirname(__DIR__);
$target  = $rootDir . '/app.php';

if (!is_file($target)) {
    http_response_code(500);
    echo "<h1>app.php absent du bundle</h1><pre>" . implode("\n", scandir($rootDir)) . "</pre>";
    exit;
}
chdir($rootDir);
require $target;